<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\PengajuanLainnya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovePengajuanLainController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $approverId = $user->id;

        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'pengajuan_id' => ['required', 'integer', 'min:1'],
            'alasan_penolakan' => ['nullable', 'string', 'max:2000'],
        ]);

        $item = PengajuanLainnya::find($data['pengajuan_id']);

        if (! $item) {
            return response()->json(['status' => 'error', 'message' => 'Data permohonan tidak ditemukan.']);
        }

        if ($item->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Permohonan ini telah berstatus '.$item->status.'.',
            ]);
        }

        $isSuperAdmin = (int) $user->role_id === 1;

        if ((int) $item->target_approver_id > 0
            && (int) $item->target_approver_id !== $approverId
            && ! $isSuperAdmin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak! Anda bukan Approver yang ditunjuk ('.e($item->target_approver_name).') untuk permohonan ini.',
            ], 403);
        }

        if ($data['action'] === 'approve') {
            $item->update([
                'status' => 'approved',
                'approved_by_id' => $approverId,
                'approved_by_name' => $user->nama,
                'approved_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Pengajuan dispensasi berhasil DISETUJUI.']);
        }

        if (empty($data['alasan_penolakan'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Harap berikan catatan alasan penolakan.',
            ], 422);
        }

        $item->update([
            'status' => 'rejected',
            'approved_by_id' => $approverId,
            'approved_by_name' => $user->nama,
            'approved_at' => now(),
            'alasan_penolakan' => $data['alasan_penolakan'],
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pengajuan dispensasi telah DITOLAK.']);
    }
}
