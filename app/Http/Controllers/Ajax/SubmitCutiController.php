<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\CutiKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmitCutiController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        $data = $request->validate([
            'tipe_cuti' => ['required', 'in:tahunan,sakit,melahirkan,izin'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date'],
            'alasan' => ['required', 'string', 'max:5000'],
            'target_approver_id' => ['required', 'integer', 'min:1'],
            'lampiran_dokumen' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ]);

        $cfg = CutiKaryawan::tipeConfig()[$data['tipe_cuti']];
        $karyawanId = $user->id;

        if ($data['tanggal_selesai'] < $data['tanggal_mulai']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai',
            ], 422);
        }

        $totalHari = CutiKaryawan::hitungTotalHari($data['tanggal_mulai'], $data['tanggal_selesai']);
        $potongKuota = $cfg['potong_kuota'];

        // Aturan: cuti khusus (sakit/melahirkan/izin) WAJIB upload bukti; tahunan opsional
        $hasUpload = $request->hasFile('lampiran_dokumen');

        if (! $potongKuota && ! $hasUpload) {
            return response()->json([
                'status' => 'error',
                'message' => 'Untuk pengajuan cuti khusus (sakit / melahirkan / izin), Anda WAJIB mengunggah bukti foto / surat dokter / dokumen pendukung!',
            ], 422);
        }

        $lampiranPath = null;
        if ($hasUpload) {
            $file = $request->file('lampiran_dokumen');
            $lampiranPath = 'cuti_'.$karyawanId.'_'.now()->format('Ymd_His').'_'.bin2hex(random_bytes(4)).'.'.strtolower($file->getClientOriginalExtension());
            $file->storeAs('documents', $lampiranPath, 'public');
        }

        // Validasi nama approver
        $approver = Karyawan::find($data['target_approver_id']);
        $targetApproverName = $approver?->nama ?? 'Atasan / HRD';

        // Validasi overlap cuti aktif (pending / approved)
        $overlap = CutiKaryawan::where('karyawan_id', $karyawanId)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('tanggal_mulai', [$data['tanggal_mulai'], $data['tanggal_selesai']])
                    ->orWhereBetween('tanggal_selesai', [$data['tanggal_mulai'], $data['tanggal_selesai']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('tanggal_mulai', '<=', $data['tanggal_mulai'])
                            ->where('tanggal_selesai', '>=', $data['tanggal_mulai']);
                    });
            })
            ->first();

        if ($overlap) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda telah memiliki pengajuan cuti yang masih aktif / disetujui pada rentang tanggal tersebut ('.$overlap->tanggal_mulai->toDateString().' s/d '.$overlap->tanggal_selesai->toDateString().')',
            ], 422);
        }

        // Jika cuti tahunan: validasi kuota
        if ($potongKuota) {
            $tahunPengajuan = (int) substr($data['tanggal_mulai'], 0, 4);
            $kuota = $user->rekapKuotaCuti($tahunPengajuan);
            $sisaKuota = $kuota['remaining_quota'];

            if ($totalHari > $sisaKuota) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sisa kuota cuti tahunan Anda tidak mencukupi! Jatah kuota: '.$kuota['total_quota'].' hari/tahun, Terpakai: '.$kuota['used_quota'].' hari, Sisa: '.$sisaKuota.' hari. Anda mengajukan: '.$totalHari.' hari.',
                ], 422);
            }
        }

        $cuti = CutiKaryawan::create([
            'karyawan_id' => $karyawanId,
            'kategori' => $cfg['kategori'],
            'tipe_cuti' => $data['tipe_cuti'],
            'label_tipe' => $cfg['label'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'total_hari' => $totalHari,
            'potong_kuota_tahunan' => $potongKuota,
            'target_approver_id' => $data['target_approver_id'],
            'target_approver_name' => $targetApproverName,
            'alasan' => $data['alasan'],
            'lampiran_dokumen' => $lampiranPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permohonan '.$cfg['label'].' sebanyak '.$totalHari.' hari berhasil diajukan ke '.$targetApproverName.' dan menunggu persetujuan.',
            'data' => [
                'id' => $cuti->id,
                'kategori' => $cuti->kategori,
                'tipe_cuti' => $cuti->tipe_cuti,
                'total_hari' => $totalHari,
                'potong_kuota' => $potongKuota,
                'lampiran_dokumen' => $lampiranPath,
                'target_approver_name' => $targetApproverName,
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'status' => 'pending',
            ],
        ]);
    }
}
