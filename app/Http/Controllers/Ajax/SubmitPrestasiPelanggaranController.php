<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\PrestasiPelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmitPrestasiPelanggaranController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        $data = $request->validate([
            'kategori' => ['required', 'in:prestasi,pelanggaran'],
            'subjek' => ['required', 'in:siswa,guru'],
            'siswa_id' => ['nullable', 'integer', 'exists:siswa,id'],
            'guru_id' => ['nullable', 'integer', 'exists:karyawan,id'],
            'nama_manual' => ['nullable', 'string', 'max:150'],
            'judul' => ['required', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'foto_bukti' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'foto_bukti.required' => 'Foto bukti wajib diunggah.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
        ]);

        // Subjek guru hanya boleh dicatat & diinput oleh admin
        if ($data['subjek'] === 'guru' && ! $user->isAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Hanya admin yang dapat mencatat prestasi/pelanggaran guru.'], 403);
        }

        // Guru wajib presensi masuk dahulu, kecuali hari ini shift-nya day off
        if (! $user->isAdmin() && $user->kodeRoleGuru() && ! $this->sudahAbsenHariIni($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus melakukan presensi masuk terlebih dahulu sebelum mencatat prestasi/pelanggaran.',
            ], 403);
        }

        if ($data['subjek'] === 'siswa') {
            $data['guru_id'] = null;
            if (empty($data['siswa_id']) && mb_trim((string) $data['nama_manual']) === '') {
                return response()->json(['status' => 'error', 'message' => 'Pilih siswa dari daftar atau isi nama manual.'], 422);
            }
        } else {
            $data['siswa_id'] = null;
            $data['nama_manual'] = null;
            if (empty($data['guru_id'])) {
                return response()->json(['status' => 'error', 'message' => 'Pilih guru terlebih dahulu.'], 422);
            }
        }

        $data['foto_bukti'] = $request->file('foto_bukti')->store('prestasi', 'public');

        $catatan = PrestasiPelanggaran::create($data + ['pelapor_id' => $user->id]);

        return response()->json([
            'status' => 'success',
            'message' => ucfirst($data['kategori']).' untuk '.$catatan->namaSubjek().' berhasil dicatat.',
            'foto_bukti' => $catatan->foto_bukti,
        ]);
    }

    /**
     * Guru dianggap boleh mencatat bila sudah presensi masuk hari ini,
     * atau roster hari ini adalah day off.
     */
    private function sudahAbsenHariIni($user): bool
    {
        $today = now()->toDateString();

        if ($user->presensi()->whereDate('tanggal', $today)->where('tipe', 'masuk')->exists()) {
            return true;
        }

        $roster = $user->jadwalShift()->where('tanggal', $today)->with('shift')->first();

        return (bool) $roster?->shift?->is_off;
    }
}
