<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PrestasiPelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPrestasiController extends Controller
{
    /**
     * Data 6 panel rekap prestasi/pelanggaran; dipakai halaman laporan & tab dashboard.
     */
    public static function reportData(Request $request): array
    {
        $dari = $request->date('dari')?->toDateString() ?? now()->startOfYear()->toDateString();
        $sampai = $request->date('sampai')?->toDateString() ?? now()->toDateString();

        return [
            'siswaBerprestasi' => self::rankingSiswa('prestasi', $dari, $sampai),
            'siswaPelanggar' => self::rankingSiswa('pelanggaran', $dari, $sampai),
            'guruBerprestasi' => self::rankingGuru('prestasi', $dari, $sampai),
            'guruPelanggar' => self::rankingGuru('pelanggaran', $dari, $sampai),
            'guruRajinMelapor' => PrestasiPelanggaran::query()
                ->selectRaw('pelapor_id, COUNT(*) as total_catatan,
                    SUM(kategori = "prestasi") as total_prestasi,
                    SUM(kategori = "pelanggaran") as total_pelanggaran')
                ->whereBetween('tanggal', [$dari, $sampai])
                ->groupBy('pelapor_id')
                ->orderByDesc('total_catatan')
                ->limit(15)
                ->with('pelapor:id,nama,nip')
                ->get(),
            'muridRajin' => Siswa::query()
                ->selectRaw('siswa.id, siswa.nis, siswa.nama, siswa.kelas, siswa.nama_orang_tua, siswa.is_active, siswa.created_at, SUM(kategori = "prestasi") as total_prestasi, COUNT(pp.id) as total_catatan')
                ->leftJoin('prestasi_pelanggaran as pp', function ($join) use ($dari, $sampai) {
                    $join->on('siswa.id', '=', 'pp.siswa_id')
                        ->whereBetween('pp.tanggal', [$dari, $sampai]);
                })
                ->groupBy('siswa.id', 'siswa.nis', 'siswa.nama', 'siswa.kelas', 'siswa.nama_orang_tua', 'siswa.is_active', 'siswa.created_at')
                ->having('total_catatan', '>', 0)
                ->orderByDesc('total_catatan')
                ->orderByDesc('total_prestasi')
                ->limit(15)
                ->get(),
            'dari' => $dari,
            'sampai' => $sampai,
        ];
    }

    public function __invoke(Request $request): View
    {
        return view('admin.laporan-prestasi', self::reportData($request));
    }

    private static function rankingSiswa(string $kategori, string $dari, string $sampai): mixed
    {
        return Siswa::query()
            ->selectRaw('siswa.id, siswa.nis, siswa.nama, siswa.kelas, siswa.nama_orang_tua, siswa.is_active, siswa.created_at, COUNT(pp.id) as total')
            ->join('prestasi_pelanggaran as pp', function ($join) use ($kategori, $dari, $sampai) {
                $join->on('siswa.id', '=', 'pp.siswa_id')
                    ->where('pp.kategori', $kategori)
                    ->whereBetween('pp.tanggal', [$dari, $sampai]);
            })
            ->groupBy('siswa.id', 'siswa.nis', 'siswa.nama', 'siswa.kelas', 'siswa.nama_orang_tua', 'siswa.is_active', 'siswa.created_at')
            ->orderByDesc('total')
            ->limit(15)
            ->get();
    }

    private static function rankingGuru(string $kategori, string $dari, string $sampai): mixed
    {
        return Karyawan::query()
            ->selectRaw('karyawan.id, karyawan.nip, karyawan.nama, karyawan.departemen, karyawan.jabatan, COUNT(pp.id) as total')
            ->join('prestasi_pelanggaran as pp', function ($join) use ($kategori, $dari, $sampai) {
                $join->on('karyawan.id', '=', 'pp.guru_id')
                    ->where('pp.kategori', $kategori)
                    ->whereBetween('pp.tanggal', [$dari, $sampai]);
            })
            ->groupBy('karyawan.id', 'karyawan.nip', 'karyawan.nama', 'karyawan.departemen', 'karyawan.jabatan')
            ->orderByDesc('total')
            ->limit(15)
            ->get();
    }
}
