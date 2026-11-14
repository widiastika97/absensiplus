<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\JadwalLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetJadwalLiburController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);

        $monthStr = sprintf('%04d-%02d', $tahun, $bulan);

        $holidays = JadwalLibur::query()
            ->whereMonth('tanggal_mulai', $bulan)->whereYear('tanggal_mulai', $tahun)
            ->orWhere(function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal_selesai', $bulan)->whereYear('tanggal_selesai', $tahun);
            })
            ->orderBy('tanggal_mulai')
            ->get()
            ->filter(fn (JadwalLibur $h) => $h->berlakuUntuk($user))
            ->values();

        return response()->json([
            'status' => 'success',
            'karyawan' => ['id' => $user->id, 'nama' => $user->nama, 'agama' => $user->agama],
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total' => $holidays->count(),
            'data' => $holidays,
        ]);
    }
}
