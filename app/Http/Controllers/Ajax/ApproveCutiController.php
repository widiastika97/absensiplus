<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\CutiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApproveCutiController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        if (! $user->hasPermission('admin_persetujuan_cuti')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses persetujuan cuti',
            ], 403);
        }

        $data = $request->validate([
            'cuti_id' => ['required', 'integer', 'min:1'],
            'action' => ['required', 'in:approve,reject'],
            'alasan_penolakan' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['action'] === 'reject' && empty($data['alasan_penolakan'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Harap sertakan alasan atau catatan penolakan',
            ], 422);
        }

        $cuti = CutiKaryawan::with('karyawan')->find($data['cuti_id']);

        if (! $cuti) {
            return response()->json(['status' => 'error', 'message' => 'Data permohonan cuti tidak ditemukan']);
        }

        $isSuperAdmin = (int) $user->role_id === 1;

        // Validasi approver yang ditunjuk pemohon
        if ((int) $cuti->target_approver_id > 0
            && (int) $cuti->target_approver_id !== (int) $user->id
            && ! $isSuperAdmin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak! Anda bukan Approver yang ditunjuk ('.e($cuti->target_approver_name).') untuk permohonan cuti ini. Anda hanya berhak melihat (View Only).',
            ], 403);
        }

        $newStatus = $data['action'] === 'approve' ? 'approved' : 'rejected';

        $cuti->update([
            'status' => $newStatus,
            'approved_by_id' => $user->id,
            'approved_by_name' => $user->nama,
            'approved_at' => now(),
            'alasan_penolakan' => $newStatus === 'rejected' ? $data['alasan_penolakan'] : null,
        ]);

        $pesan = $newStatus === 'approved'
            ? 'Permohonan '.$cuti->label_tipe.' untuk '.$cuti->karyawan->nama.' berhasil DISETUJUI.'
            : 'Permohonan '.$cuti->label_tipe.' untuk '.$cuti->karyawan->nama.' telah DITOLAK.';

        return response()->json([
            'status' => 'success',
            'message' => $pesan,
            'data' => [
                'id' => $cuti->id,
                'karyawan_nama' => $cuti->karyawan->nama,
                'new_status' => $newStatus,
                'approved_by_name' => $user->nama,
                'approved_at' => now()->format('Y-m-d H:i:s'),
                'alasan_penolakan' => $newStatus === 'rejected' ? $data['alasan_penolakan'] : null,
            ],
        ]);
    }
}
