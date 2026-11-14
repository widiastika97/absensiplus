<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\MasterShift;
use App\Models\LokasiKantor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RoleSeeder::class, JadwalLiburSeeder::class]);

        $shifts = [
            ['id' => 1, 'kode_shift' => 'S-PAGI', 'nama_shift' => 'Normal Office / Pagi', 'jam_masuk' => '08:00:00', 'jam_pulang' => '17:00:00', 'is_night_shift' => 0, 'is_off' => 0, 'warna_badge' => 'emerald', 'deskripsi' => 'Jam kerja operasional normal 08:00 - 17:00 WITA.'],
            ['id' => 2, 'kode_shift' => 'S-SIANG', 'nama_shift' => 'Shift Siang', 'jam_masuk' => '13:00:00', 'jam_pulang' => '21:00:00', 'is_night_shift' => 0, 'is_off' => 0, 'warna_badge' => 'blue', 'deskripsi' => 'Jam kerja operasional siang hingga malam WITA.'],
            ['id' => 3, 'kode_shift' => 'S-MALAM', 'nama_shift' => 'Shift Malam (Night)', 'jam_masuk' => '21:00:00', 'jam_pulang' => '05:00:00', 'is_night_shift' => 1, 'is_off' => 0, 'warna_badge' => 'purple', 'deskripsi' => 'Shift malam lintas hari untuk tim teknis & support 24/7.'],
            ['id' => 4, 'kode_shift' => 'S-MIDDLE', 'nama_shift' => 'Shift Middle', 'jam_masuk' => '10:00:00', 'jam_pulang' => '18:00:00', 'is_night_shift' => 0, 'is_off' => 0, 'warna_badge' => 'amber', 'deskripsi' => 'Shift fleksibel jam sibuk operasional.'],
            ['id' => 5, 'kode_shift' => 'S-OFF', 'nama_shift' => 'Libur Shift (Day Off)', 'jam_masuk' => '00:00:00', 'jam_pulang' => '00:00:00', 'is_night_shift' => 0, 'is_off' => 1, 'warna_badge' => 'slate', 'deskripsi' => 'Jadwal hari libur pengganti roster kerja.'],
        ];

        foreach ($shifts as $shift) {
            MasterShift::updateOrCreate(['id' => $shift['id']], $shift);
        }

        $lokasi = [
            ['id' => 1, 'kode_lokasi' => 'KTR-JKT', 'nama_lokasi' => 'Kantor Pusat - Jakarta (Sudirman)', 'alamat' => 'Menara Astra Lt. 24, Jl. Jend. Sudirman Kav. 5-6, Karet Tengsin', 'kota' => 'Jakarta Pusat', 'latitude' => -6.2088, 'longitude' => 106.8227, 'radius' => 100, 'is_active' => 1],
            ['id' => 2, 'kode_lokasi' => 'KTR-BDG', 'nama_lokasi' => 'Kantor Cabang - Bandung (Dago)', 'alamat' => 'Jl. Ir. H. Juanda No. 128, Dago, Coblong', 'kota' => 'Bandung', 'latitude' => -6.8856, 'longitude' => 107.6139, 'radius' => 75, 'is_active' => 1],
            ['id' => 3, 'kode_lokasi' => 'KTR-SBY', 'nama_lokasi' => 'Kantor Cabang - Surabaya (Gubeng)', 'alamat' => 'Jl. Pemuda No. 33-37, Embong Kaliasin, Genteng', 'kota' => 'Surabaya', 'latitude' => -7.2655, 'longitude' => 112.7486, 'radius' => 80, 'is_active' => 1],
        ];

        foreach ($lokasi as $loc) {
            LokasiKantor::updateOrCreate(['id' => $loc['id']], $loc);
        }

        $karyawan = [
            ['id' => 1, 'nip' => 'ADM-001', 'nama' => 'Administrator Utama', 'email' => 'admin@perusahaan.com', 'departemen' => 'Management', 'jabatan' => 'System Administrator', 'role_id' => 1, 'agama' => 'Islam', 'no_hp' => '08111222333', 'kuota_cuti_tahunan' => 12, 'is_active' => 1],
            ['id' => 2, 'nip' => 'EMP-2026001', 'nama' => 'Budi Santoso, S.Kom', 'email' => 'budi.santoso@perusahaan.co.id', 'departemen' => 'IT & Engineering', 'jabatan' => 'Senior Software Engineer', 'role_id' => 4, 'agama' => 'Islam', 'no_hp' => '081234567890', 'kuota_cuti_tahunan' => 12, 'is_active' => 1],
            ['id' => 3, 'nip' => 'EMP-2026002', 'nama' => 'Siti Rahmawati, S.E.', 'email' => 'siti.rahma@perusahaan.co.id', 'departemen' => 'Finance & Accounting', 'jabatan' => 'Finance Supervisor', 'role_id' => 3, 'agama' => 'Islam', 'no_hp' => '081298765432', 'kuota_cuti_tahunan' => 12, 'is_active' => 1],
            ['id' => 4, 'nip' => 'EMP-2026003', 'nama' => 'Ahmad Fauzi, S.T.', 'email' => 'ahmad.fauzi@perusahaan.co.id', 'departemen' => 'Human Resources', 'jabatan' => 'HR Specialist', 'role_id' => 2, 'agama' => 'Islam', 'no_hp' => '081311223344', 'kuota_cuti_tahunan' => 12, 'is_active' => 1],
        ];

        foreach ($karyawan as $k) {
            Karyawan::updateOrCreate(
                ['id' => $k['id']],
                $k + ['password' => Hash::make('123456')]
            );
        }
    }
}
