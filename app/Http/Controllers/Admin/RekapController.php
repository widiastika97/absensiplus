<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatatanAbsensi;
use App\Models\CutiKaryawan;
use App\Models\JadwalLibur;
use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapController extends Controller
{
    public function index(Request $request): View
    {
        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);
        $dept = $request->query('dept', 'all');

        $rekap = Presensi::with(['karyawan:id,nama,nip,departemen', 'lokasi:id,nama_lokasi'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when($dept !== 'all', fn ($q) => $q->whereHas('karyawan', fn ($k) => $k->where('departemen', $dept)))
            ->orderByDesc('tanggal')
            ->orderBy('jam')
            ->get();

        $departemenList = Karyawan::distinct()->orderBy('departemen')->pluck('departemen');

        // Catatan admin (sistem eror / karyawan lupa absen) per karyawan+tanggal
        $catatanMap = CatatanAbsensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get()
            ->keyBy(fn ($c) => $c->karyawan_id.'_'.$c->tanggal->toDateString());

        return view('admin.rekap', [
            'rekap' => $rekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'dept' => $dept,
            'departemenList' => $departemenList,
            'catatanMap' => $catatanMap,
        ]);
    }

    /**
     * Detail presensi karyawan per bulan (kalender + grid).
     */
    public function detail(Request $request): View
    {
        $karyawanId = (int) $request->query('karyawan_id', 0);
        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);

        $allKaryawan = Karyawan::orderBy('nama')->get(['id', 'nama', 'nip', 'departemen']);

        if ($karyawanId <= 0 && $allKaryawan->isNotEmpty()) {
            $karyawanId = $allKaryawan->first()->id;
        }

        $karyawan = Karyawan::with('role')->findOrFail($karyawanId);

        $startDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $daysInMonth = (int) now()->createFromDate($tahun, $bulan, 1)->daysInMonth;
        $endDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $daysInMonth);

        $presensi = Presensi::with('lokasi')
            ->where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($p) => $p->tanggal->toDateString());

        // Libur nasional/keagamaan yang berlaku untuk karyawan bulan ini
        $liburList = JadwalLibur::whereBetween('tanggal_mulai', [$startDate, $endDate])
            ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
            ->get()
            ->filter(fn ($l) => $l->berlakuUntuk($karyawan));

        // Cuti approved karyawan bulan ini
        $cutiList = $karyawan->cutiKaryawan()
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
            })->get();

        // Catatan admin (sistem eror / karyawan lupa absen) per tanggal
        $catatanMap = CatatanAbsensi::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get()
            ->keyBy(fn ($c) => $c->tanggal->toDateString());

        // Roster shift tercatat (menentukan hari kerja/libur karyawan, termasuk hari Minggu)
        $rosterMap = \App\Models\JadwalShiftKaryawan::with('shift')
            ->where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy(fn ($r) => $r->tanggal->toDateString());

        // Statistik kehadiran bulan ini
        $stats = ['hadir' => 0, 'tepat_waktu' => 0, 'terlambat' => 0, 'cuti' => 0, 'libur' => 0];
        $today = now()->toDateString();
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $tglStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
            if ($tglStr > $today) {
                continue;
            }
            $masuk = $presensi->get($tglStr)?->firstWhere('tipe', 'masuk');
            $rosterTgl = $rosterMap->get($tglStr);
            if ($masuk) {
                $stats['hadir']++;
                $masuk->status === 'terlambat' ? $stats['terlambat']++ : $stats['tepat_waktu']++;
            } elseif ($this->cutiPada($tglStr, $cutiList)) {
                $stats['cuti']++;
            } elseif ($this->liburPada($tglStr, $liburList) || ($rosterTgl && $rosterTgl->shift->is_off)) {
                $stats['libur']++;
            }
        }

        $prev = $bulan > 1 ? ['bulan' => $bulan - 1, 'tahun' => $tahun] : ['bulan' => 12, 'tahun' => $tahun - 1];
        $next = $bulan < 12 ? ['bulan' => $bulan + 1, 'tahun' => $tahun] : ['bulan' => 1, 'tahun' => $tahun + 1];

        return view('admin.detail-absensi', [
            'karyawan' => $karyawan,
            'allKaryawan' => $allKaryawan,
            'presensi' => $presensi,
            'liburList' => $liburList,
            'cutiList' => $cutiList,
            'catatanMap' => $catatanMap,
            'rosterMap' => $rosterMap,
            'stats' => $stats,
            'prev' => $prev,
            'next' => $next,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'daysInMonth' => $daysInMonth,
        ]);
    }

    private function liburPada(string $tglStr, $liburList): ?JadwalLibur
    {
        foreach ($liburList as $l) {
            if ($tglStr >= $l->tanggal_mulai->toDateString() && $tglStr <= $l->tanggal_selesai->toDateString()) {
                return $l;
            }
        }

        return null;
    }

    private function cutiPada(string $tglStr, $cutiList): ?CutiKaryawan
    {
        foreach ($cutiList as $c) {
            if ($tglStr >= $c->tanggal_mulai->toDateString() && $tglStr <= $c->tanggal_selesai->toDateString()) {
                return $c;
            }
        }

        return null;
    }
}
