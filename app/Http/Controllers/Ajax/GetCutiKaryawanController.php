<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\CutiKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetCutiKaryawanController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $tahun = (int) $request->query('tahun', now()->year);
        $requestedKaryawanId = (int) $request->query('karyawan_id', 0);

        // Non HR/Admin: paksa hanya melihat miliknya sendiri
        $targetKaryawanId = $user->hasPermission('admin_persetujuan_cuti')
            ? ($requestedKaryawanId > 0 ? $requestedKaryawanId : 0)
            : $user->id;

        if ($targetKaryawanId > 0) {
            $leaves = CutiKaryawan::query()
                ->with('karyawan:id,nama,nip,departemen,jabatan')
                ->where('karyawan_id', $targetKaryawanId)
                ->whereYear('tanggal_mulai', $tahun)
                ->orderByDesc('created_at')
                ->get();

            $karyawan = Karyawan::find($targetKaryawanId);
            $kuota = $karyawan
                ? $karyawan->rekapKuotaCuti($tahun)
                : ['total_quota' => 12, 'used_quota' => 0, 'pending_quota' => 0, 'remaining_quota' => 12, 'special_days' => 0];

            return response()->json([
                'status' => 'success',
                'tahun' => $tahun,
                'quota' => [
                    'annual_quota_total' => $kuota['total_quota'],
                    'annual_quota_used' => $kuota['used_quota'],
                    'annual_quota_pending' => $kuota['pending_quota'],
                    'annual_quota_remaining' => $kuota['remaining_quota'],
                    'special_leave_days' => $kuota['special_days'],
                ],
                'data' => $leaves,
            ]);
        }

        // Semua karyawan (HR/Admin)
        $leaves = CutiKaryawan::query()
            ->with('karyawan:id,nama,nip,departemen,jabatan')
            ->whereYear('tanggal_mulai', $tahun)
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();

        $employees = Karyawan::where('is_active', 1)->orderBy('nama')->get(['id', 'nip', 'nama', 'departemen', 'kuota_cuti_tahunan']);

        $employeeQuotas = $employees->map(function ($emp) use ($tahun) {
            $kuota = $emp->rekapKuotaCuti($tahun);

            return [
                'karyawan_id' => $emp->id,
                'nip' => $emp->nip,
                'nama' => $emp->nama,
                'departemen' => $emp->departemen,
                'total_quota' => $kuota['total_quota'],
                'used_quota' => $kuota['used_quota'],
                'pending_quota' => $kuota['pending_quota'],
                'remaining_quota' => $kuota['remaining_quota'],
                'special_days' => $kuota['special_days'],
            ];
        });

        return response()->json([
            'status' => 'success',
            'tahun' => $tahun,
            'employee_quotas' => $employeeQuotas,
            'total_pending' => $leaves->where('status', 'pending')->count(),
            'data' => $leaves,
        ]);
    }
}
