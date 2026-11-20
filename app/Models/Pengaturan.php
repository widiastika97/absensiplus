<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['kunci', 'nilai'];

    public static function get(string $kunci, ?string $default = null): ?string
    {
        return static::query()->where('kunci', $kunci)->value('nilai') ?? $default;
    }

    public static function set(string $kunci, ?string $nilai): void
    {
        static::query()->updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
    }

    /**
     * Preset jam kerja dari pengaturan (fallback ke mapping RoleSeeder).
     */
    public static function jamKerjaJenis(?string $kodeRoleJenis): ?array
    {
        $map = [
            'ROLE_KARYAWAN_TETAP' => ['jam_karyawan_tetap_masuk', 'jam_karyawan_tetap_pulang'],
            'ROLE_KARYAWAN_KONTRAK' => ['jam_karyawan_kontrak_masuk', 'jam_karyawan_kontrak_pulang'],
        ];

        if (! isset($map[$kodeRoleJenis])) {
            return null;
        }

        return [
            static::get($map[$kodeRoleJenis][0], '07:30'),
            static::get($map[$kodeRoleJenis][1], '14:30'),
        ];
    }

    /**
     * Jam kerja berdasarkan role di tabel roles (untuk query bulk).
     */
    public static function mapJamPerRoleKode(): array
    {
        return [
            'ROLE_KARYAWAN_TETAP' => ['masuk' => static::get('jam_karyawan_tetap_masuk', '07:30'), 'pulang' => static::get('jam_karyawan_tetap_pulang', '14:30')],
            'ROLE_KARYAWAN_KONTRAK' => ['masuk' => static::get('jam_karyawan_kontrak_masuk', '07:15'), 'pulang' => static::get('jam_karyawan_kontrak_pulang', '15:00')],
        ];
    }
}
