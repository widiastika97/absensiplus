<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatatanAbsensi;
use App\Models\CutiKaryawan;
use App\Models\JadwalLibur;
use App\Models\JadwalShiftKaryawan;
use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Export rekapitulasi matriks kehadiran ke Excel (.xls via HTML table)
 * dan Print/PDF (HTML siap cetak dengan window.print).
 */
class ExportController extends Controller
{
    public function excel(Request $request): Response
    {
        $data = $this->buildMatrix($request);
        $tanggalMulai = $data['tanggal_mulai'];
        $tanggalSelesai = $data['tanggal_selesai'];

        $filename = 'Rekap_Absensi_'.$tanggalMulai.'_sd_'.$tanggalSelesai.'.xls';

        return response()
            ->view('admin.exports.rekap-matrix', $data)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename='.$filename)
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function pdf(Request $request): Response
    {
        $data = $this->buildMatrix($request);
        $data['isPrint'] = true;

        return response()->view('admin.exports.rekap-matrix', $data);
    }

    private function buildMatrix(Request $request): array
    {
        $dates = Validator::make([
            'tanggal_mulai' => $request->query('tanggal_mulai', $request->query('start_date')),
            'tanggal_selesai' => $request->query('tanggal_selesai', $request->query('end_date')),
        ], [
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d', 'required_with:tanggal_selesai'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'required_with:tanggal_mulai', 'after_or_equal:tanggal_mulai'],
        ])->validate();
        $tanggalMulai = $dates['tanggal_mulai'] ?? '';
        $tanggalSelesai = $dates['tanggal_selesai'] ?? '';

        // Backward compatibility: bulan & tahun
        if ($request->filled(['bulan', 'tahun']) && empty($tanggalMulai)) {
            $tanggalMulai = sprintf('%04d-%02d-01', (int) $request->query('tahun'), (int) $request->query('bulan'));
            $tanggalSelesai = date('Y-m-t', strtotime($tanggalMulai));
        }

        $tanggalMulai = $tanggalMulai ?: now()->startOfMonth()->toDateString();
        $tanggalSelesai = $tanggalSelesai ?: now()->toDateString();

        if ($tanggalMulai > $tanggalSelesai) {
            [$tanggalMulai, $tanggalSelesai] = [$tanggalSelesai, $tanggalMulai];
        }

        // Daftar tanggal
        $daftarTanggal = [];
        $cur = new \DateTime($tanggalMulai);
        $end = new \DateTime($tanggalSelesai);
        while ($cur <= $end) {
            $daftarTanggal[] = $cur->format('Y-m-d');
            $cur->modify('+1 day');
        }

        // Karyawan aktif
        $karyawanList = Karyawan::where('is_active', 1)
            ->orderBy('departemen')->orderBy('nama')
            ->get(['id', 'nama', 'nip', 'departemen', 'jabatan']);

        // Presensi dalam rentang tanggal
        $presensiMap = [];
        Presensi::whereDate('tanggal', '>=', $tanggalMulai)
            ->whereDate('tanggal', '<=', $tanggalSelesai)
            ->orderBy('tanggal')->orderBy('jam')
            ->get(['karyawan_id', 'tanggal', 'tipe', 'jam', 'status', 'keterangan'])
            ->each(function ($p) use (&$presensiMap) {
                $presensiMap[$p->karyawan_id][$p->tanggal->toDateString()][$p->tipe] = $p;
            });

        // Cuti approved
        $cutiMap = [];
        CutiKaryawan::where('status', 'approved')
            ->whereDate('tanggal_mulai', '<=', $tanggalSelesai)
            ->whereDate('tanggal_selesai', '>=', $tanggalMulai)
            ->get(['karyawan_id', 'label_tipe', 'tanggal_mulai', 'tanggal_selesai'])
            ->each(function ($c) use (&$cutiMap, $tanggalMulai, $tanggalSelesai) {
                $start = max($c->tanggal_mulai->toDateString(), $tanggalMulai);
                $endC = min($c->tanggal_selesai->toDateString(), $tanggalSelesai);
                $cur = new \DateTime($start);
                $endD = new \DateTime($endC);
                while ($cur <= $endD) {
                    $cutiMap[$c->karyawan_id][$cur->format('Y-m-d')] = $c->label_tipe ?: 'Cuti';
                    $cur->modify('+1 day');
                }
            });

        // Libur nasional
        $liburMap = [];
        JadwalLibur::whereDate('tanggal_mulai', '<=', $tanggalSelesai)
            ->whereDate('tanggal_selesai', '>=', $tanggalMulai)
            ->get(['nama_libur', 'tanggal_mulai', 'tanggal_selesai'])
            ->each(function ($l) use (&$liburMap, $tanggalMulai, $tanggalSelesai) {
                $start = max($l->tanggal_mulai->toDateString(), $tanggalMulai);
                $endL = min($l->tanggal_selesai->toDateString(), $tanggalSelesai);
                $cur = new \DateTime($start);
                $endD = new \DateTime($endL);
                while ($cur <= $endD) {
                    $liburMap[$cur->format('Y-m-d')] = $l->nama_libur;
                    $cur->modify('+1 day');
                }
            });

        // Hari off dari roster shift per karyawan (sumber libur selain jadwal libur)
        $rosterOffMap = [];
        JadwalShiftKaryawan::with('shift')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get()
            ->each(function ($r) use (&$rosterOffMap) {
                if ($r->shift && $r->shift->is_off) {
                    $rosterOffMap[$r->karyawan_id][$r->tanggal->toDateString()] = true;
                }
            });

        // Catatan absensi admin (mis. sistem eror / lupa absen)
        $catatanList = CatatanAbsensi::whereDate('tanggal', '>=', $tanggalMulai)
            ->whereDate('tanggal', '<=', $tanggalSelesai)
            ->orderBy('tanggal')->get(['karyawan_id', 'tanggal', 'catatan']);

        // Estimasi lebar matriks (px): kolom identitas 540 + 4 rekap 180 + kolom tanggal ~46px + margin
        $jumlahTanggal = count($daftarTanggal);
        $estimasiLebar = 760 + 46 * $jumlahTanggal;
        $printZoom = min(1, round(1040 / $estimasiLebar, 2));

        return [
            'daftarTanggal' => $daftarTanggal,
            'karyawanList' => $karyawanList,
            'presensiMap' => $presensiMap,
            'cutiMap' => $cutiMap,
            'liburMap' => $liburMap,
            'rosterOffMap' => $rosterOffMap,
            'catatanList' => $catatanList,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'today' => now()->toDateString(),
            'isPrint' => false,
            'printZoom' => $printZoom,
        ];
    }
}
