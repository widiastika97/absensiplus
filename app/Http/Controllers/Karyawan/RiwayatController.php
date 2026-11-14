<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::guard('karyawan')->user();

        $preset = $request->query('preset', 'bulan_ini');
        $startDate = $request->query('start_date', '');
        $endDate = $request->query('end_date', '');

        if (empty($startDate) || empty($endDate)) {
            [$startDate, $endDate] = match ($preset) {
                'hari_ini' => [now()->toDateString(), now()->toDateString()],
                '7_hari' => [now()->subDays(7)->toDateString(), now()->toDateString()],
                '30_hari' => [now()->subDays(30)->toDateString(), now()->toDateString()],
                'semua' => ['2020-01-01', now()->toDateString()],
                default => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            };
            $preset = $preset === 'custom' ? 'custom' : $preset;
        }

        $lokasiList = LokasiKantor::orderBy('nama_lokasi')->get();

        $logs = Presensi::with('lokasi')
            ->where('karyawan_id', $user->id)
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when((int) $request->query('lokasi_id', 0), fn ($q, $l) => $q->where('lokasi_id', $l))
            ->when(trim((string) $request->query('search')), function ($q, $search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('lokasi', fn ($lq) => $lq->where('nama_lokasi', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->get();

        // Kelompokkan per tanggal: masuk + pulang
        $grouped = $logs->groupBy('tanggal')->sortKeysDesc();

        $kpi = [
            'total' => $logs->count(),
            'tepat_waktu' => $logs->where('status', 'tepat_waktu')->count(),
            'terlambat' => $logs->where('status', 'terlambat')->count(),
        ];

        return view('karyawan.riwayat', [
            'user' => $user,
            'grouped' => $grouped,
            'kpi' => $kpi,
            'preset' => $preset,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'lokasiList' => $lokasiList,
            'filters' => [
                'status' => $request->query('status', ''),
                'lokasi_id' => $request->query('lokasi_id', ''),
                'search' => $request->query('search', ''),
            ],
        ]);
    }
}
