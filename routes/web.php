<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\JadwalLiburController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\SeederAbsensiController;
use App\Http\Controllers\Ajax\ApproveCutiController;
use App\Http\Controllers\Ajax\ApprovePengajuanLainController;
use App\Http\Controllers\Ajax\CheckRadiusController;
use App\Http\Controllers\Ajax\CrudKaryawanController;
use App\Http\Controllers\Ajax\CrudLokasiController;
use App\Http\Controllers\Ajax\CrudRoleController;
use App\Http\Controllers\Ajax\CrudShiftController;
use App\Http\Controllers\Ajax\GetCutiKaryawanController;
use App\Http\Controllers\Ajax\GetJadwalLiburController;
use App\Http\Controllers\Ajax\GetRiwayatPresensiController;
use App\Http\Controllers\Ajax\SimpanAbsenController;
use App\Http\Controllers\Ajax\SubmitCutiController;
use App\Http\Controllers\Ajax\SubmitPengajuanLainController;
use App\Http\Controllers\Ajax\SubmitPrestasiPelanggaranController;
use App\Http\Controllers\Admin\LaporanPrestasiController;
use App\Http\Controllers\Karyawan\PrestasiPelanggaranController;
use App\Http\Controllers\Ajax\UpdateFotoProfilController;
use App\Http\Controllers\Ajax\UpdateSaldoCutiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Karyawan\AbsensiController;
use App\Http\Controllers\Karyawan\PengajuanCutiController;
use App\Http\Controllers\Karyawan\ProfilController;
use App\Http\Controllers\Karyawan\RiwayatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest:karyawan')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Karyawan (memerlukan login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:karyawan')->group(function () {
    // Presensi (geofence + selfie)
    Route::get('absen', [AbsensiController::class, 'index'])->name('absen');

    // AJAX presensi
    Route::post('ajax/check-radius', CheckRadiusController::class)->name('ajax.check-radius');
    Route::post('ajax/simpan-absen', SimpanAbsenController::class)->name('ajax.simpan-absen');

    // Riwayat presensi
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('ajax/riwayat-presensi', GetRiwayatPresensiController::class)->name('ajax.riwayat-presensi');

    // Prestasi & pelanggaran (dashboard 3 tab: Absen / Prestasi / Pelanggaran)
    Route::get('prestasi', PrestasiPelanggaranController::class)->defaults('kategori', 'prestasi')->name('prestasi');
    Route::get('pelanggaran', PrestasiPelanggaranController::class)->defaults('kategori', 'pelanggaran')->name('pelanggaran');
    Route::post('ajax/submit-prestasi-pelanggaran', SubmitPrestasiPelanggaranController::class)->name('ajax.submit-prestasi-pelanggaran');

    // Profil
    Route::get('profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('ajax/update-foto-profil', UpdateFotoProfilController::class)->name('ajax.update-foto-profil');

    // Pengajuan cuti & dispensasi
    Route::get('pengajuan-cuti', [PengajuanCutiController::class, 'index'])
        ->middleware('permission:pengajuan_cuti_karyawan,pengajuan_cuti_pribadi')
        ->name('pengajuan-cuti');
    Route::post('ajax/submit-cuti', SubmitCutiController::class)
        ->middleware('permission:pengajuan_cuti_karyawan,pengajuan_cuti_pribadi')
        ->name('ajax.submit-cuti');
    Route::post('ajax/submit-pengajuan-lain', SubmitPengajuanLainController::class)->name('ajax.submit-pengajuan-lain');

    // Info cuti & libur
    Route::get('ajax/cuti-karyawan', GetCutiKaryawanController::class)->name('ajax.cuti-karyawan');
    Route::get('ajax/jadwal-libur', GetJadwalLiburController::class)->name('ajax.jadwal-libur');

    // Laporan prestasi & pelanggaran (role yang diberi izin, tampil di portal absen)
    Route::get('laporan-prestasi', LaporanPrestasiController::class)
        ->middleware('permission:laporan_prestasi_pelanggaran')->name('laporan-prestasi');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:karyawan', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // AJAX CRUD
    Route::post('ajax/crud-karyawan', CrudKaryawanController::class)
        ->middleware('permission:admin_data_karyawan')->name('ajax.crud-karyawan');
    Route::post('ajax/crud-lokasi', CrudLokasiController::class)
        ->middleware('permission:admin_titik_kantor')->name('ajax.crud-lokasi');
    Route::post('ajax/crud-role', CrudRoleController::class)
        ->middleware('permission:admin_manajemen_role')->name('ajax.crud-role');
Route::post('ajax/crud-shift', CrudShiftController::class)
        ->middleware('permission:admin_data_karyawan')->name('ajax.crud-shift');

    // Persetujuan cuti & dispensasi
    Route::post('ajax/approve-cuti', ApproveCutiController::class)
        ->middleware('permission:admin_persetujuan_cuti')->name('ajax.approve-cuti');
    Route::post('ajax/approve-pengajuan-lain', ApprovePengajuanLainController::class)
        ->middleware('permission:admin_persetujuan_cuti')->name('ajax.approve-pengajuan-lain');
    Route::post('ajax/update-saldo-cuti', UpdateSaldoCutiController::class)
        ->middleware('permission:admin_persetujuan_cuti,admin_data_karyawan')->name('ajax.update-saldo-cuti');

    // Master jadwal libur (full CRUD)
    Route::get('jadwal-libur', [JadwalLiburController::class, 'index'])
        ->middleware('permission:admin_jadwal_libur')->name('jadwal-libur.index');
    Route::post('jadwal-libur', [JadwalLiburController::class, 'store'])
        ->middleware('permission:admin_jadwal_libur')->name('jadwal-libur.store');
    Route::put('jadwal-libur/{jadwalLibur}', [JadwalLiburController::class, 'update'])
        ->middleware('permission:admin_jadwal_libur')->name('jadwal-libur.update');
    Route::delete('jadwal-libur/{jadwalLibur}', [JadwalLiburController::class, 'destroy'])
        ->middleware('permission:admin_jadwal_libur')->name('jadwal-libur.destroy');

    // Rekap & ekspor
    Route::get('rekap', [RekapController::class, 'index'])
        ->middleware('permission:admin_rekap_ekspor')->name('rekap');
    Route::get('detail-absensi', [RekapController::class, 'detail'])
        ->name('detail-absensi');

    Route::get('export/excel', [ExportController::class, 'excel'])
        ->middleware('permission:admin_rekap_ekspor')->name('export.excel');
    Route::get('export/pdf', [ExportController::class, 'pdf'])
        ->middleware('permission:admin_rekap_ekspor')->name('export.pdf');

    // Seeder absensi dummy
    Route::post('seeder-absensi', SeederAbsensiController::class)->name('seeder-absensi');
});
