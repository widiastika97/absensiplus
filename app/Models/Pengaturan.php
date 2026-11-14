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
     * Preset jam kerja guru dari pengaturan (fallback ke mapping RoleSeeder).
     */
    public static function jamKerjaGuru(?string $kodeRoleGuru): ?array
    {
        $map = [
            'ROLE_GURU_TETAP' => ['jam_guru_tetap_masuk', 'jam_guru_tetap_pulang'],
            'ROLE_GURU_KONTRAK' => ['jam_guru_kontrak_masuk', 'jam_guru_kontrak_pulang'],
        ];

        if (! isset($map[$kodeRoleGuru])) {
            return null;
        }

        return [
            static::get($map[$kodeRoleGuru][0], '07:30'),
            static::get($map[$kodeRoleGuru][1], '14:30'),
        ];
    }

    /**
     * Jam kerja berdasarkan role di tabel roles (untuk query bulk).
     */
    public static function mapJamPerRoleKode(): array
    {
        return [
            'ROLE_GURU_TETAP' => ['masuk' => static::get('jam_guru_tetap_masuk', '07:30'), 'pulang' => static::get('jam_guru_tetap_pulang', '14:30')],
            'ROLE_GURU_KONTRAK' => ['masuk' => static::get('jam_guru_kontrak_masuk', '07:15'), 'pulang' => static::get('jam_guru_kontrak_pulang', '15:00')],
        ];
    }
}
