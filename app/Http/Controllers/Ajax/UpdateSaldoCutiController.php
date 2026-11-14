<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateSaldoCutiController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        if (! $user->hasPermission('admin_persetujuan_cuti') && ! $user->hasPermission('admin_data_karyawan')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Anda tidak memiliki otoritas mengubah saldo kuota cuti.',
            ], 403);
        }

        $data = $request->validate([
            'karyawan_id' => ['required', 'integer', 'min:1'],
            'kuota_baru' => ['required', 'integer', 'min:0'],
        ], [
            'kuota_baru.min' => 'Data karyawan atau angka kuota saldo tidak valid (minimal 0).',
        ]);

        $emp = Karyawan::find($data['karyawan_id']);

        if (! $emp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan dengan ID tersebut tidak ditemukan di database.',
            ]);
        }

        $kuotaLama = (int) $emp->kuota_cuti_tahunan;

        $emp->update(['kuota_cuti_tahunan' => $data['kuota_baru']]);

        $usedDays = $emp->cutiKaryawan()
            ->where('status', 'approved')
            ->where('potong_kuota_tahunan', 1)
            ->whereYear('tanggal_mulai', now()->year)
            ->sum('total_hari');

        return response()->json([
            'status' => 'success',
            'message' => 'Jatah saldo cuti tahunan karyawan '.$emp->nama.' berhasil diperbarui menjadi '.$data['kuota_baru'].' hari (Sebelumnya: '.$kuotaLama.' hari).',
            'data' => [
                'karyawan_id' => $emp->id,
                'nama' => $emp->nama,
                'nip' => $emp->nip,
                'departemen' => $emp->departemen,
                'kuota_lama' => $kuotaLama,
                'kuota_total' => $data['kuota_baru'],
                'kuota_terpakai' => (int) $usedDays,
                'sisa_kuota' => max(0, $data['kuota_baru'] - (int) $usedDays),
            ],
        ]);
    }
}
