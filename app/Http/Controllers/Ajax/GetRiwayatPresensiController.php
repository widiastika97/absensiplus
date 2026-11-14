<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetRiwayatPresensiController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        $query = Presensi::query()
            ->with('lokasi:id,nama_lokasi,alamat,radius')
            ->where('karyawan_id', $user->id);

        if ($startDate = $request->query('start_date', now()->startOfMonth()->toDateString())) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        if ($endDate = $request->query('end_date', now()->endOfMonth()->toDateString())) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        if ($tipe = $request->query('tipe')) {
            $query->where('tipe', $tipe);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($lokasiId = (int) $request->query('lokasi_id', 0)) {
            $query->where('lokasi_id', $lokasiId);
        }

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('lokasi', fn ($lq) => $lq->where('nama_lokasi', 'like', "%{$search}%"))
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderByDesc('tanggal')->orderByDesc('jam')->get();

        $total = $logs->count();
        $masuk = $logs->where('tipe', 'masuk')->count();
        $pulang = $logs->where('tipe', 'pulang')->count();
        $tepatWaktu = $logs->where('status', 'tepat_waktu')->count();
        $terlambat = $logs->where('status', 'terlambat')->count();

        return response()->json([
            'status' => 'success',
            'karyawan_id' => $user->id,
            'periode' => [
                'start_date' => $request->query('start_date', now()->startOfMonth()->toDateString()),
                'end_date' => $request->query('end_date', now()->endOfMonth()->toDateString()),
            ],
            'kpi' => [
                'total' => $total,
                'masuk' => $masuk,
                'pulang' => $pulang,
                'tepat_waktu' => $tepatWaktu,
                'terlambat' => $terlambat,
                'persentase_disiplin' => $total > 0 ? round(($tepatWaktu / $total) * 100, 1) : 100,
            ],
            'total' => $total,
            'data' => $logs,
        ]);
    }
}
