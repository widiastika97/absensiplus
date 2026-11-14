<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\JadwalShiftKaryawan;
use App\Models\LokasiKantor;
use App\Models\MasterShift;
use App\Models\Presensi;
use App\Services\GeofenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SimpanAbsenController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $karyawanId = $user->id;

        $data = $request->validate([
            'tipe' => ['nullable', 'in:masuk,pulang'],
            'image_data' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], [
            'image_data.required' => 'Data tidak lengkap (Foto selfie & GPS wajib ada)',
            'latitude.required' => 'Data tidak lengkap (Foto selfie & GPS wajib ada)',
            'longitude.required' => 'Data tidak lengkap (Foto selfie & GPS wajib ada)',
        ]);

        $tipe = $data['tipe'] ?? 'masuk';
        $userLat = (float) $data['latitude'];
        $userLng = (float) $data['longitude'];

        // 1. Verifikasi ulang jarak radius di sisi server
        [$kantorValid, $jarakTervalidasi] = GeofenceService::findValidOffice($userLat, $userLng);

        if (! $kantorValid instanceof LokasiKantor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal absen! Posisi koordinat Anda berada di luar radius semua kantor resmi.',
            ]);
        }

        // 2. Simpan foto selfie base64 ke storage
        if (! preg_match('/^data:image\/(\w+);base64,/', $data['image_data'], $m)) {
            return response()->json(['status' => 'error', 'message' => 'Format foto selfie base64 rusak']);
        }

        $imageBinary = base64_decode(substr($data['image_data'], strlen($m[0])), true);

        if ($imageBinary === false) {
            return response()->json(['status' => 'error', 'message' => 'Format foto selfie base64 rusak']);
        }

        $fileName = 'selfie_'.$karyawanId.'_'.now()->format('Ymd_His').'_'.uniqid().'.jpg';
        Storage::disk('public')->put('selfie/'.$fileName, $imageBinary);

        // 3. Cegah dobel absen & validasi absen pulang
        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');
        $existingTipe = Presensi::where('karyawan_id', $karyawanId)
            ->where('tanggal', $today)
            ->pluck('tipe');

        if ($tipe === 'masuk' && $existingTipe->contains('masuk')) {
            Storage::disk('public')->delete('selfie/'.$fileName);

            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan presensi masuk hari ini!',
            ]);
        }

        if ($tipe === 'pulang') {
            if (! $existingTipe->contains('masuk')) {
                Storage::disk('public')->delete('selfie/'.$fileName);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal presensi pulang! Anda belum melakukan presensi masuk hari ini.',
                ]);
            }

            if ($existingTipe->contains('pulang')) {
                Storage::disk('public')->delete('selfie/'.$fileName);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah melakukan presensi pulang hari ini!',
                ]);
            }
        }

        // 4. Ambil penugasan shift hari ini (roster dinamis)
        $rosterHariIni = JadwalShiftKaryawan::with('shift')
            ->where('karyawan_id', $karyawanId)
            ->where('tanggal', $today)
            ->first();

        $status = 'tepat_waktu';

        if ($rosterHariIni) {
            if ($rosterHariIni->shift->is_off) {
                Storage::disk('public')->delete('selfie/'.$fileName);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Hari ini adalah jadwal Libur Shift / Day Off Anda ('.$rosterHariIni->shift->nama_shift.'). Tidak perlu presensi.',
                ]);
            }

            $jamBatasMasuk = $rosterHariIni->shift->jam_masuk;
        } else {
            // Default tanpa roster: Minggu = libur (day off)
            if ((int) now()->format('N') === 7) {
                Storage::disk('public')->delete('selfie/'.$fileName);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Hari ini adalah hari Minggu (Libur Roster / Day Off). Tidak perlu melakukan presensi.',
                ]);
            }

            $defShift = MasterShift::where('is_off', 0)->orderBy('id')->first();
            $jamBatasMasuk = $defShift?->jam_masuk ?? '08:00:00';
        }

        if ($tipe === 'masuk') {
            $status = strtotime($nowTime) > strtotime($jamBatasMasuk) ? 'terlambat' : 'tepat_waktu';
        }

        // 5. Simpan ke database
        $presensi = Presensi::create([
            'karyawan_id' => $karyawanId,
            'lokasi_id' => $kantorValid->id,
            'tanggal' => $today,
            'tipe' => $tipe,
            'jam' => $nowTime,
            'foto_selfie' => $fileName,
            'latitude' => $userLat,
            'longitude' => $userLng,
            'jarak_meter' => $jarakTervalidasi,
            'radius_diizinkan' => $kantorValid->radius,
            'status' => $status,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi '.ucfirst($tipe).' berhasil tercatat!',
            'data' => [
                'lokasi' => $kantorValid->nama_lokasi,
                'jarak' => $jarakTervalidasi.' meter',
                'jam' => $nowTime,
                'status' => $status,
                'foto' => $fileName,
            ],
        ]);
    }
}
