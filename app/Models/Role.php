<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'kode_role',
        'nama_role',
        'deskripsi',
        'warna_badge',
        'izin_akses',
        'is_system',
    ];

    protected $casts = [
        'izin_akses' => 'array',
        'is_system' => 'boolean',
    ];

    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'role_id');
    }

    public function hasPermission(string $key): bool
    {
        // Alias izin pengajuan cuti (kompatibilitas app lama)
        if ($key === 'pengajuan_cuti_pribadi' && in_array('pengajuan_cuti_karyawan', $this->izin_akses ?? [])) {
            return true;
        }

        return in_array($key, $this->izin_akses ?? []);
    }
}
