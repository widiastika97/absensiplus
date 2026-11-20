<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CutiKaryawan;
use App\Models\JadwalLibur;
use App\Models\Karyawan;
use App\Models\LokasiKantor;use App\Models\PengajuanLainnya;
use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::guard('karyawan')->user();
        $today = now()->toDateString();

        $stats = [
            'total_karyawan' => Karyawan::count(),
            'total_lokasi' => LokasiKantor::where('is_active', 1)->count(),
            'total_hadir_hari_ini' => Presensi::where('tanggal', $today)->distinct('karyawan_id')->count('karyawan_id'),
            'total_terlambat' => Presensi::where('tanggal', $today)->where('status', 'terlambat')->count(),
        ];
        $stats['attendance_rate'] = $stats['total_karyawan'] > 0
            ? (int) round(($stats['total_hadir_hari_ini'] / $stats['total_karyawan']) * 100)
            : 0;

        // Log absensi per karyawan dengan filter tanggal & departemen
        $logDari = $request->date('dari')?->toDateString() ?? $today;
        $logSampai = $request->date('sampai')?->toDateString() ?? $today;
        $logDept = $request->query('dept', 'all');
        if ($logSampai < $logDari) {
            [$logDari, $logSampai] = [$logSampai, $logDari];
        }

        $presensiHariIni = Karyawan::query()
            ->leftJoin('presensi as pm', function ($join) use ($logDari, $logSampai) {
                $join->on('karyawan.id', '=', 'pm.karyawan_id')->whereBetween('pm.tanggal', [$logDari, $logSampai])->where('pm.tipe', 'masuk');
            })
            ->leftJoin('presensi as pp', function ($join) use ($logDari, $logSampai) {
                $join->on('karyawan.id', '=', 'pp.karyawan_id')->whereBetween('pp.tanggal', [$logDari, $logSampai])->where('pp.tipe', 'pulang');
            })
            ->leftJoin('cuti_karyawan as c', function ($join) use ($logDari, $logSampai) {
                $join->on('karyawan.id', '=', 'c.karyawan_id')->where('c.status', 'approved')
                    ->whereDate('c.tanggal_mulai', '<=', $logSampai)->whereDate('c.tanggal_selesai', '>=', $logDari);
            })
            ->whereHas('presensi', fn ($q) => $q->whereBetween('tanggal', [$logDari, $logSampai]))
            ->when($logDept !== 'all', fn ($q) => $q->where('karyawan.departemen', $logDept))
            ->selectRaw('karyawan.id AS karyawan_id, karyawan.nama, karyawan.nip, karyawan.departemen, karyawan.jabatan, karyawan.foto_profil,
                MIN(pm.tanggal) AS log_tanggal,
                COUNT(DISTINCT pm.tanggal) AS hari_hadir,
                SUM(pm.status = "terlambat") AS total_terlambat,
                MIN(CASE WHEN pm.status = "terlambat" THEN pm.jam END) AS jam_telat_pertama,
                MIN(pm.jam) AS jam_masuk, MAX(CASE WHEN pm.status = "terlambat" THEN pm.id END) AS telat_id,
                MIN(pm.id) AS masuk_id, MIN(pm.foto_selfie) AS foto_masuk, MIN(pm.jarak_meter) AS jarak_masuk,
                MAX(pp.jam) AS jam_pulang, MAX(pp.id) AS pulang_id, MAX(pp.foto_selfie) AS foto_pulang, MAX(pp.jarak_meter) AS jarak_pulang,
                MIN(c.id) AS cuti_id, MIN(c.tipe_cuti) AS tipe_cuti, MIN(c.alasan) AS alasan_cuti')
            ->groupBy('karyawan.id', 'karyawan.nama', 'karyawan.nip', 'karyawan.departemen', 'karyawan.jabatan', 'karyawan.foto_profil')
            ->orderBy('karyawan.nama')
            ->get();

        $lokasiList = LokasiKantor::orderBy('id')->get();

        $departemenList = Karyawan::distinct()->orderBy('departemen')->pluck('departemen')->filter();

        $rolesList = Role::query()
            ->selectRaw('roles.id, roles.kode_role, roles.nama_role, roles.deskripsi, roles.warna_badge, roles.izin_akses, roles.is_system,
                COUNT(karyawan.id) as total_users')
            ->leftJoin('karyawan', 'roles.id', '=', 'karyawan.role_id')
            ->groupBy('roles.id', 'roles.kode_role', 'roles.nama_role', 'roles.deskripsi', 'roles.warna_badge', 'roles.izin_akses', 'roles.is_system')
            ->orderBy('roles.id')
            ->get();

        $karyawanList = Karyawan::with('role')->orderBy('id')->get();

        // Matriks Roster Bulanan (porting dari app asli shift_management.php)
        $rosterBulan = (int) $request->query('roster_bulan', now()->month);
        $rosterTahun = (int) $request->query('roster_tahun', now()->year);
        $rosterPrev = $rosterBulan > 1 ? ['bulan' => $rosterBulan - 1, 'tahun' => $rosterTahun] : ['bulan' => 12, 'tahun' => $rosterTahun - 1];
        $rosterNext = $rosterBulan < 12 ? ['bulan' => $rosterBulan + 1, 'tahun' => $rosterTahun] : ['bulan' => 1, 'tahun' => $rosterTahun + 1];

        $rosterMap = [];
        \App\Models\JadwalShiftKaryawan::with('shift')
            ->whereYear('tanggal', $rosterTahun)->whereMonth('tanggal', $rosterBulan)
            ->get()
            ->each(function ($r) use (&$rosterMap) {
                $rosterMap[$r->karyawan_id.'_'.$r->tanggal->toDateString()] = [
                    'shift_id' => $r->shift_id,
                    'kode_shift' => $r->shift->kode_shift,
                    'is_off' => $r->shift->is_off,
                ];
            });

        $daysInRosterMonth = (int) now()->createFromDate($rosterTahun, $rosterBulan, 1)->daysInMonth;

        $jamKerja = [
            'tetap' => [
                'masuk' => Pengaturan::get('jam_guru_tetap_masuk', '07:30'),
                'pulang' => Pengaturan::get('jam_guru_tetap_pulang', '14:30'),
            ],
            'kontrak' => [
                'masuk' => Pengaturan::get('jam_guru_kontrak_masuk', '07:15'),
                'pulang' => Pengaturan::get('jam_guru_kontrak_pulang', '15:00'),
            ],
        ];

        $allPermissions = [
            'presensi_masuk_pulang' => 'Absensi GPS',
            'riwayat_presensi_pribadi' => 'Riwayat User',
            'pengajuan_cuti_karyawan' => 'Pengajuan Cuti',
            'admin_persetujuan_cuti' => 'Persetujuan Cuti',
            'admin_titik_kantor' => 'Titik Kantor',
            'admin_data_karyawan' => 'Data Karyawan',
            'admin_jadwal_libur' => 'Jadwal Libur',
            'admin_manajemen_role' => 'Role RBAC',
            'admin_log_presensi' => 'Log Audit',
            'admin_rekap_ekspor' => 'Ekspor Laporan',
        ];

        $jadwalLiburList = JadwalLibur::orderBy('tanggal_mulai')->get();

        $pendingCutiList = CutiKaryawan::with('karyawan:id,nama,nip,departemen,jabatan')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $allCutiList = CutiKaryawan::with('karyawan:id,nama,nip,departemen,jabatan')
            ->whereYear('tanggal_mulai', now()->year)
            ->orderByDesc('created_at')
            ->get();

        $pendingDispensasiList = PengajuanLainnya::with('karyawan:id,nama,nip,departemen,jabatan')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $allDispensasiList = PengajuanLainnya::with('karyawan:id,nama,nip,departemen,jabatan')
            ->whereYear('tanggal', now()->year)
            ->orderByDesc('created_at')
            ->get();

        // Rekap kuota cuti semua karyawan
        $kuotaRekap = $karyawanList->where('is_active', 1)->map(function ($k) {
            return [
                'karyawan' => $k,
                'kuota' => $k->rekapKuotaCuti(now()->year),
            ];
        })->values();

        return view('admin.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'logDari' => $logDari,
            'logSampai' => $logSampai,
            'logDept' => $logDept,
            'departemenList' => $departemenList,
            'presensiHariIni' => $presensiHariIni,
            'lokasiList' => $lokasiList,
            'rolesList' => $rolesList,
            'karyawanList' => $karyawanList,
            'jamKerja' => $jamKerja,
            'rosterBulan' => $rosterBulan,
            'rosterTahun' => $rosterTahun,
            'rosterPrev' => $rosterPrev,
            'rosterNext' => $rosterNext,
            'rosterMap' => $rosterMap,
            'daysInRosterMonth' => $daysInRosterMonth,
            'shiftsListJS' => \App\Models\MasterShift::orderBy('is_off')->orderBy('jam_masuk')->get(),
            'allPermissions' => $allPermissions,
            'jadwalLiburList' => $jadwalLiburList,
            'pendingCutiList' => $pendingCutiList,
            'allCutiList' => $allCutiList,
            'pendingDispensasiList' => $pendingDispensasiList,
            'allDispensasiList' => $allDispensasiList,
            'kuotaRekap' => $kuotaRekap,
            'filterTanggal' => $request->query('tanggal', $today),
        ]);
    }
}
