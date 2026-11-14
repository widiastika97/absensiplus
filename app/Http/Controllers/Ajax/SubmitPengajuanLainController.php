<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PengajuanLainnya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmitPengajuanLainController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        $data = $request->validate([
            'tipe_pengajuan' => ['required', 'in:terlambat,pulang_cepat'],
            'target_approver_id' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'jam_estimasi' => ['required'],
            'alasan' => ['required', 'string', 'max:2000'],
            'foto_bukti' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $karyawanId = $user->id;
        $tipePengajuan = $data['tipe_pengajuan'];

        // Foto bukti: wajib untuk terlambat, opsional untuk pulang lebih awal
        $hasFile = $request->hasFile('foto_bukti');

        if ($tipePengajuan === 'terlambat' && ! $hasFile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Foto bukti kendala WAJIB diunggah untuk pengajuan Terlambat Masuk Kerja!',
            ], 422);
        }

        $fotoPath = null;
        if ($hasFile) {
            $file = $request->file('foto_bukti');
            $fotoPath = 'dispensasi_'.$tipePengajuan.'_'.$karyawanId.'_'.now()->format('Ymd_His').'.'.strtolower($file->getClientOriginalExtension());
            $file->storeAs('documents', $fotoPath, 'public');
        }

        $targetApproverName = Karyawan::find($data['target_approver_id'])?->nama ?? 'Approver';
        $labelTipe = $tipePengajuan === 'terlambat' ? 'Terlambat Masuk Kerja' : 'Pulang Lebih Awal';

        PengajuanLainnya::create([
            'karyawan_id' => $karyawanId,
            'tipe_pengajuan' => $tipePengajuan,
            'label_tipe' => $labelTipe,
            'tanggal' => $data['tanggal'],
            'jam_estimasi' => $data['jam_estimasi'],
            'target_approver_id' => $data['target_approver_id'],
            'target_approver_name' => $targetApproverName,
            'alasan' => $data['alasan'],
            'foto_bukti' => $fotoPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan '.$labelTipe.' berhasil dikirim ke '.$targetApproverName.' untuk diproses.',
        ]);
    }
}
