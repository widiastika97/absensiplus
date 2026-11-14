<?php

namespace Database\Seeders;

use App\Models\JadwalLibur;
use Illuminate\Database\Seeder;

class JadwalLiburSeeder extends Seeder
{
    public function run(): void
    {
        $libur = [
            [
                'nama_libur' => 'Hari Kemerdekaan Republik Indonesia Ke-81',
                'tanggal_mulai' => '2026-08-17',
                'tanggal_selesai' => '2026-08-17',
                'tipe_target' => 'semua',
                'target_agama' => null,
                'target_karyawan_ids' => null,
                'deskripsi' => 'Hari Libur Nasional memperingati Kemerdekaan RI.',
            ],
            [
                'nama_libur' => 'Cuti Bersama Hari Kemerdekaan',
                'tanggal_mulai' => '2026-08-18',
                'tanggal_selesai' => '2026-08-18',
                'tipe_target' => 'semua',
                'target_agama' => null,
                'target_karyawan_ids' => null,
                'deskripsi' => 'Cuti bersama tahunan perayaan kemerdekaan RI.',
            ],
        ];

        foreach ($libur as $l) {
            JadwalLibur::updateOrCreate(
                ['nama_libur' => $l['nama_libur']],
                $l
            );
        }
    }
}
