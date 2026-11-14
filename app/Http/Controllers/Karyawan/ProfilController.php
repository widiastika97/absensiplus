<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\LokasiKantor;
use App\Models\PengajuanLainnya;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::guard('karyawan')->user()->load('role');
        $today = now()->toDateString();

        // Lokasi kantor dari presensi terakhir, fallback lokasi aktif pertama
        $lokasiKantor = Presensi::with('lokasi')
            ->where('karyawan_id', $user->id)
            ->orderByDesc('id')
            ->first()?->lokasi?->nama_lokasi
            ?? optional(LokasiKantor::where('is_active', 1)->orderBy('id')->first())->nama_lokasi
            ?? 'Kantor Pusat - Jakarta';

        $shiftHariIni = $user->shiftHariIni($today)?->load('shift');

        $kuota = $user->rekapKuotaCuti(now()->year);
        $totalHadirBulanIni = Presensi::where('karyawan_id', $user->id)
            ->where('tipe', 'masuk')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->distinct('tanggal')
            ->count('tanggal');

        $riwayatPengajuanLain = PengajuanLainnya::where('karyawan_id', $user->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->get();

        $approverList = Karyawan::query()
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereIn('role_id', [1, 2, 3])
                    ->orWhereHas('role', fn ($r) => $r->where('izin_akses', 'like', '%admin_persetujuan_cuti%'));
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'jabatan', 'departemen', 'role_id']);

        $totalCutiPending = $user->cutiKaryawan()->where('status', 'pending')->count();

        return view('karyawan.profil', [
            'user' => $user,
            'lokasiKantor' => $lokasiKantor,
            'shiftHariIni' => $shiftHariIni,
            'kuota' => $kuota,
            'totalHadirBulanIni' => $totalHadirBulanIni,
            'riwayatPengajuanLain' => $riwayatPengajuanLain,
            'approverList' => $approverList,
            'totalCutiPending' => $totalCutiPending,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('karyawan')->user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
