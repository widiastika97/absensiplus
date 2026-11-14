<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PrestasiPelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrestasiPelanggaranController extends Controller
{
    /**
     * Dashboard 3 tab (Absen / Prestasi / Pelanggaran) milik guru.
     * Kategori guru (subjek = guru) hanya terlihat oleh admin.
     */
    public function __invoke(Request $request, string $kategori): View
    {
        abort_unless(in_array($kategori, ['prestasi', 'pelanggaran']), 404);

        $user = Auth::guard('karyawan')->user();

        $query = PrestasiPelanggaran::with(['guru', 'siswa', 'pelapor'])
            ->where('kategori', $kategori)
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        // Catatan subjek guru hanya tampil untuk admin
        if (! $user->isAdmin()) {
            $query->where('subjek', 'siswa');
        }

        $dataList = $query
            ->when($request->filled('q'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('judul', 'like', '%'.$request->q.'%')
                    ->orWhere('keterangan', 'like', '%'.$request->q.'%')
                    ->orWhere('nama_manual', 'like', '%'.$request->q.'%')
                    ->orWhereHas('siswa', fn ($s) => $s->where('nama', 'like', '%'.$request->q.'%'))
                    ->orWhereHas('guru', fn ($s) => $s->where('nama', 'like', '%'.$request->q.'%'));
            }))
            ->when($request->filled('tipe_subjek'), fn ($q) => $q->where('subjek', $request->tipe_subjek))
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->sampai))
            ->limit(200)
            ->get();

        return view('karyawan.prestasi', [
            'kategori' => $kategori,
            'dataList' => $dataList,
            'siswaList' => Siswa::where('is_active', 1)->orderBy('nama')->get(),
            'guruList' => $user->isAdmin()
                ? Karyawan::where('is_active', 1)->orderBy('nama')->get(['id', 'nama', 'nip'])
                : collect(),
            'isAdmin' => $user->isAdmin(),
        ]);
    }
}
