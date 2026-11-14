<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class KaryawanFactory extends Factory
{
    protected $model = Karyawan::class;

    public function definition(): array
    {
        return [
            'nip' => 'EMP-'.fake()->unique()->numerify('######'),
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'departemen' => fake()->randomElement(['IT', 'HR', 'Finance']),
            'jabatan' => fake()->jobTitle(),
            'role_id' => 4,
            'agama' => 'Islam',
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
            'kuota_cuti_tahunan' => 12,
            'is_active' => true,
        ];
    }
}
