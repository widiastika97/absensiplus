<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\JadwalShiftKaryawan;
use App\Models\Karyawan;
use App\Models\MasterShift;
use App\Models\Role;
use Illuminate\Http\Request;

class CrudShiftController extends Controller
{
    public function __invoke(Request $request)
    {
        $action = $request->input('action', $request->query('action'));

        return match ($action) {
            'tambah_shift' => $this->tambahShift($request),
            'edit_shift' => $this->editShift($request),
            'hapus_shift' => $this->hapusShift($request),
            'assign_roster_single' => $this->assignRosterSingle($request),
            'generate_pola_bulanan', 'auto_generate_semua_default' => $this->generatePolaBulanan($request),
            default => response()->json(['status' => 'error', 'message' => 'Aksi tidak valid!']),
        };
    }

    private function tambahShift(Request $request)
    {
        $data = $request->validate([
            'kode_shift' => ['required', 'string', 'max:50'],
            'nama_shift' => ['required', 'string', 'max:100'],
            'jam_masuk' => ['nullable'],
            'jam_pulang' => ['nullable'],
            'is_night_shift' => ['nullable', 'boolean'],
            'is_off' => ['nullable', 'boolean'],
            'warna_badge' => ['nullable', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], [
            'kode_shift.required' => 'Kode dan nama shift wajib diisi!',
            'nama_shift.required' => 'Kode dan nama shift wajib diisi!',
        ]);

        MasterShift::create([
            'kode_shift' => strtoupper($data['kode_shift']),
            'nama_shift' => $data['nama_shift'],
            'jam_masuk' => $data['jam_masuk'] ?? '08:00:00',
            'jam_pulang' => $data['jam_pulang'] ?? '17:00:00',
            'is_night_shift' => (bool) ($data['is_night_shift'] ?? false),
            'is_off' => (bool) ($data['is_off'] ?? false),
            'warna_badge' => $data['warna_badge'] ?? 'blue',
            'deskripsi' => $data['deskripsi'] ?? null,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Master shift berhasil ditambahkan!']);
    }

    private function editShift(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'nama_shift' => ['required', 'string', 'max:100'],
            'jam_masuk' => ['nullable'],
            'jam_pulang' => ['nullable'],
            'is_night_shift' => ['nullable', 'boolean'],
            'is_off' => ['nullable', 'boolean'],
            'warna_badge' => ['nullable', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ]);

        $shift = MasterShift::findOrFail($data['id']);
        $shift->update([
            'nama_shift' => $data['nama_shift'],
            'jam_masuk' => $data['jam_masuk'] ?? $shift->jam_masuk,
            'jam_pulang' => $data['jam_pulang'] ?? $shift->jam_pulang,
            'is_night_shift' => (bool) ($data['is_night_shift'] ?? false),
            'is_off' => (bool) ($data['is_off'] ?? false),
            'warna_badge' => $data['warna_badge'] ?? $shift->warna_badge,
            'deskripsi' => $data['deskripsi'] ?? $shift->deskripsi,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Master shift berhasil diperbarui!']);
    }

    private function hapusShift(Request $request)
    {
        $data = $request->validate(['id' => ['required', 'integer', 'min:1']]);

        $shift = MasterShift::withCount('jadwalKaryawan')->findOrFail($data['id']);

        if ($shift->jadwal_karyawan_count > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shift tidak dapat dihapus karena sudah digunakan dalam jadwal roster karyawan!',
            ], 422);
        }

        $shift->delete();

        return response()->json(['status' => 'success', 'message' => 'Master shift berhasil dihapus!']);
    }

    private function assignRosterSingle(Request $request)
    {
        $data = $request->validate([
            'karyawan_id' => ['required', 'integer', 'min:1'],
            'shift_id' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        JadwalShiftKaryawan::updateOrCreate(
            ['karyawan_id' => $data['karyawan_id'], 'tanggal' => $data['tanggal']],
            ['shift_id' => $data['shift_id'], 'catatan' => $data['catatan'] ?? null]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Roster shift tanggal '.$data['tanggal'].' berhasil disimpan!',
        ]);
    }

    private function generatePolaBulanan(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $pola = $request->input('pola', '6_1');

        $defaultOfficeId = MasterShift::where('is_off', 0)->orderBy('id')->value('id') ?: 1;
        $defaultOffId = MasterShift::where('is_off', 1)->orderBy('id')->value('id') ?: 5;

        $shiftPagiId = (int) $request->input('shift_pagi_id', $defaultOfficeId);
        $shiftSiangId = (int) $request->input('shift_siang_id', 2);
        $shiftMalamId = (int) $request->input('shift_malam_id', 3);
        $shiftOffId = (int) $request->input('shift_off_id', $defaultOffId);

        $karyawanIds = $request->input('karyawan_ids', 'all');
        $jenis = in_array($request->input('jenis', 'semua'), ['tetap', 'kontrak'], true)
            ? $request->input('jenis', 'semua')
            : 'semua';

        if ($karyawanIds === 'all' || (is_array($karyawanIds) && (empty($karyawanIds) || in_array('all', $karyawanIds)))) {
            $query = Karyawan::where('is_active', 1);

            if ($jenis !== 'semua') {
                $kodeRole = $jenis === 'tetap' ? 'ROLE_GURU_TETAP' : 'ROLE_GURU_KONTRAK';
                $roleId = Role::where('kode_role', $kodeRole)->value('id');
                if (! $roleId) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Role '.$kodeRole.' tidak ditemukan!',
                    ], 422);
                }
                $query->where('role_id', $roleId);
            }

            $karyawanIds = $query->pluck('id')->toArray();
        }

        if (empty($karyawanIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data karyawan aktif yang ditemukan untuk digenerate jadwalnya!',
            ], 422);
        }

        $daysInMonth = (int) now()->createFromDate($tahun, $bulan, 1)->daysInMonth;
        $rows = [];

        foreach ($karyawanIds as $empId) {
            $empId = (int) $empId;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $tglStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                $dayOfWeek = (int) date('N', strtotime($tglStr)); // 1 = Senin .. 7 = Minggu
                $targetShiftId = $shiftPagiId;

                if ($pola === '6_1') {
                    $targetShiftId = ($dayOfWeek === 7) ? $shiftOffId : $shiftPagiId;
                } elseif ($pola === '5_2') {
                    $targetShiftId = ($dayOfWeek >= 6) ? $shiftOffId : $shiftPagiId;
                } elseif ($pola === 'rotasi_pagi_siang') {
                    $weekNum = (int) date('W', strtotime($tglStr));
                    $targetShiftId = ($dayOfWeek === 7)
                        ? $shiftOffId
                        : ($weekNum % 2 === 0 ? $shiftPagiId : $shiftSiangId);
                } elseif ($pola === '2p_2s_2m_2off') {
                    $cycleIndex = ($d - 1) % 8;
                    $targetShiftId = match (true) {
                        $cycleIndex < 2 => $shiftPagiId,
                        $cycleIndex < 4 => $shiftSiangId,
                        $cycleIndex < 6 => $shiftMalamId,
                        default => $shiftOffId,
                    };
                }

                $rows[] = [
                    'karyawan_id' => $empId,
                    'shift_id' => $targetShiftId,
                    'tanggal' => $tglStr,
                    'catatan' => null,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            JadwalShiftKaryawan::upsert($chunk, ['karyawan_id', 'tanggal'], ['shift_id']);
        }

        // Terapkan jam kerja karyawan tetap & kontrak dari form generate ke karyawan terkait.
        // Karyawan dengan jam kerja custom diabaikan (diatur via assign roster).
        $jamTetap = $request->input('jam_tetap_masuk') ?? $request->input('jam_guru_tetap_masuk');
        $jamKontrak = $request->input('jam_kontrak_masuk') ?? $request->input('jam_guru_kontrak_masuk');
        if ($jamTetap || $jamKontrak) {
            $kodePeta = [];
            if ($jamTetap) {
                $kodePeta['ROLE_GURU_TETAP'] = [
                    'jam_masuk' => $jamTetap,
                    'jam_pulang' => $request->input('jam_tetap_pulang', $request->input('jam_guru_tetap_pulang', '14:30')),
                ];
            }
            if ($jamKontrak) {
                $kodePeta['ROLE_GURU_KONTRAK'] = [
                    'jam_masuk' => $jamKontrak,
                    'jam_pulang' => $request->input('jam_kontrak_pulang', $request->input('jam_guru_kontrak_pulang', '15:00')),
                ];
            }

            foreach ($kodePeta as $kode => $jamHasil) {
                $roleId = Role::where('kode_role', $kode)->value('id');
                if (! $roleId) {
                    continue;
                }
                Karyawan::where('role_id', $roleId)->update([
                    'jam_masuk' => $jamHasil['jam_masuk'].':00',
                    'jam_pulang' => $jamHasil['jam_pulang'].':00',
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil meng-generate otomatis roster shift untuk '.count($karyawanIds).' karyawan pada bulan '.$bulan.'/'.$tahun.' dengan pola '.$pola.'.',
            'total_inserted' => count($rows),
        ]);
    }
}
