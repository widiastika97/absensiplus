<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\JadwalLibur;
use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::guard('karyawan')->user()->load('role');
        $today = now()->toDateString();

        // Log absensi hari ini
        $logsHariIni = Presensi::with('lokasi')
            ->where('karyawan_id', $user->id)
            ->where('tanggal', $today)
            ->orderBy('jam')
            ->get();

        $logMasuk = $logsHariIni->firstWhere('tipe', 'masuk');
        $logPulang = $logsHariIni->firstWhere('tipe', 'pulang');

        // Roster shift hari ini, fallback default (jam kerja 08:00-17:00, tanpa asumsi Minggu off)
        $rosterHariIni = $user->shiftHariIni($today)?->load('shift');

        if ($rosterHariIni) {
            $namaShiftAktif = $rosterHariIni->shift->nama_shift;
            $isShiftOff = $rosterHariIni->shift->is_off;
            $isNightShift = $rosterHariIni->shift->is_night_shift;
        } else {
            // Tanpa roster: hari kerja default (tidak ada lagi asumsi "Minggu = libur")
            $namaShiftAktif = 'Normal Office (08:00 - 17:00 WITA)';
            $isShiftOff = false;
            $isNightShift = false;
        }

        // Libur bulan ini yang relevan untuk karyawan
        $rawLibur = JadwalLibur::query()
            ->whereMonth('tanggal_mulai', now()->month)->whereYear('tanggal_mulai', now()->year)
            ->orWhere(function ($q) {
                $q->whereMonth('tanggal_selesai', now()->month)->whereYear('tanggal_selesai', now()->year);
            })
            ->orderBy('tanggal_mulai')
            ->get()
            ->filter(fn (JadwalLibur $l) => $l->berlakuUntuk($user))
            ->values();

        // Kuota cuti tahunan
        $kuota = $user->rekapKuotaCuti(now()->year);
        $totalCutiPending = $user->cutiKaryawan()->where('status', 'pending')->count();

        // Pengajuan dispensasi terbaru
        $pengajuanLain = $user->pengajuanLainnya()->orderByDesc('created_at')->limit(3)->get();

        [$windowMulai, $windowSelesai] = $user->jendelaPresensiMasuk($today);
        $sekarang = now();
        $bisaPresensiMasuk = !$isShiftOff && $sekarang->between($windowMulai, $windowSelesai);

        return view('karyawan.absen', [
            'user' => $user,
            'logMasuk' => $logMasuk,
            'logPulang' => $logPulang,
            'sudahMasuk' => $logsHariIni->contains('tipe', 'masuk'),
            'sudahPulang' => $logsHariIni->contains('tipe', 'pulang'),
            'namaShiftAktif' => $namaShiftAktif,
            'isShiftOff' => $isShiftOff,
            'isNightShift' => $isNightShift,
            'liburBulanIni' => $rawLibur,
            'kuota' => $kuota,
            'totalCutiPending' => $totalCutiPending,
            'pengajuanLain' => $pengajuanLain,
            'windowMulai'       => $windowMulai,
            'windowSelesai'     => $windowSelesai,
            'bisaPresensiMasuk' => $bisaPresensiMasuk,
            'serverNowMs'       => $sekarang->getTimestampMs(),
        ]);
    }
}
