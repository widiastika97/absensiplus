<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajuanCutiController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::guard('karyawan')->user();
        $tahunIni = now()->year;

        $kuota = $user->rekapKuotaCuti($tahunIni);

        $riwayatCuti = $user->cutiKaryawan()->orderByDesc('created_at')->get();

        $approverList = Karyawan::query()
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereIn('role_id', [1, 2, 3])
                    ->orWhereHas('role', fn ($r) => $r->where('izin_akses', 'like', '%admin_persetujuan_cuti%'));
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'jabatan', 'departemen', 'role_id']);

        return view('karyawan.pengajuan-cuti', [
            'user' => $user,
            'kuota' => $kuota,
            'riwayatCuti' => $riwayatCuti,
            'approverList' => $approverList,
        ]);
    }
}
