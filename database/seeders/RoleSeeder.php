<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Jam kerja default per jenis guru (tipe guru dikelola via role).
     *
     * @var array<string, array{0: string, 1: string}|null>
     */
    public const JAM_KERJA_GURU = [
        'ROLE_GURU_TETAP' => ['07:30:00', '14:30:00'],
        'ROLE_GURU_KONTRAK' => ['07:15:00', '15:00:00'],
        'ROLE_GURU_HONOR' => null, // custom, diatur admin per karyawan
    ];

    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'kode_role' => 'ROLE_SUPER_ADMIN',
                'nama_role' => 'Super Administrator',
                'deskripsi' => 'Memiliki akses penuh tanpa batas ke semua modul konfigurasi, master data, RBAC, cuti, dan audit presensi.',
                'warna_badge' => 'purple',
                'izin_akses' => ['presensi_masuk_pulang', 'riwayat_presensi_pribadi', 'pengajuan_cuti_karyawan', 'admin_persetujuan_cuti', 'admin_titik_kantor', 'admin_data_karyawan', 'admin_jadwal_libur', 'admin_manajemen_role', 'admin_log_presensi', 'admin_rekap_ekspor', 'laporan_prestasi_pelanggaran'],
                'is_system' => true,
            ],
            [
                'id' => 2,
                'kode_role' => 'ROLE_HR_MANAGER',
                'nama_role' => 'HR & People Manager',
                'deskripsi' => 'Akses mengelola data karyawan, persetujuan cuti & kuota tahunan, jadwal libur bersama, memantau absensi realtime, dan rekapitulasi.',
                'warna_badge' => 'blue',
                'izin_akses' => ['presensi_masuk_pulang', 'riwayat_presensi_pribadi', 'pengajuan_cuti_karyawan', 'admin_persetujuan_cuti', 'admin_data_karyawan', 'admin_jadwal_libur', 'admin_log_presensi', 'admin_rekap_ekspor', 'laporan_prestasi_pelanggaran'],
                'is_system' => false,
            ],
            [
                'id' => 3,
                'kode_role' => 'ROLE_SUPERVISOR',
                'nama_role' => 'Supervisor Operasional',
                'deskripsi' => 'Mengawasi log kehadiran tim lapangan, meninjau permohonan cuti tim, dan memantau kedisiplinan kerja.',
                'warna_badge' => 'amber',
                'izin_akses' => ['presensi_masuk_pulang', 'riwayat_presensi_pribadi', 'pengajuan_cuti_karyawan', 'admin_persetujuan_cuti', 'admin_log_presensi', 'admin_rekap_ekspor', 'laporan_prestasi_pelanggaran'],
                'is_system' => false,
            ],
            [
                'id' => 4,
                'kode_role' => 'ROLE_STAFF',
                'nama_role' => 'Staff Karyawan',
                'deskripsi' => 'Melakukan presensi masuk/pulang menggunakan selfie GPS, melihat riwayat mandiri, serta mengajukan cuti tahunan dan izin khusus.',
                'warna_badge' => 'emerald',
                'izin_akses' => ['presensi_masuk_pulang', 'riwayat_presensi_pribadi', 'pengajuan_cuti_karyawan'],
                'is_system' => true,
            ],
            [
                'id' => 5,
                'kode_role' => 'ROLE_GURU_TETAP',
                'nama_role' => 'Guru Tetap Yayasan',
                'deskripsi' => 'Guru tetap yayasan. Jam kerja 07:30 - 14:30. Presensi GPS, riwayat, cuti, serta pencatatan prestasi & pelanggaran siswa.',
                'warna_badge' => 'cyan',
                'izin_akses' => ['presensi_masuk_pulang', 'riwayat_presensi_pribadi', 'pengajuan_cuti_karyawan', 'prestasi_pelanggaran_siswa'],
                'is_system' => true,
            ],
            [
                'id' => 6,
                'kode_role' => 'ROLE_GURU_KONTRAK',
                'nama_role' => 'Guru Tetap Yayasan (Kontrak)',
                'deskripsi' => 'Guru tetap yayasan kontrak. Jam kerja 07:15 - 15:00. Presensi GPS, riwayat, cuti, serta pencatatan prestasi & pelanggaran siswa.',
                'warna_badge' => 'indigo',
                'izin_akses' => ['presensi_masuk_pulang', 'riwayat_presensi_pribadi', 'pengajuan_cuti_karyawan', 'prestasi_pelanggaran_siswa'],
                'is_system' => true,
            ],
            [
                'id' => 7,
                'kode_role' => 'ROLE_GURU_HONOR',
                'nama_role' => 'Guru Honor',
                'deskripsi' => 'Guru honor dengan jam kerja custom yang diatur admin. Presensi GPS, riwayat, cuti, serta pencatatan prestasi & pelanggaran siswa.',
                'warna_badge' => 'rose',
                'izin_akses' => ['presensi_masuk_pulang', 'riwayat_presensi_pribadi', 'pengajuan_cuti_karyawan', 'prestasi_pelanggaran_siswa'],
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}
