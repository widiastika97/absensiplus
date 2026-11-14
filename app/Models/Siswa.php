<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = ['nis', 'nama', 'kelas', 'nama_orang_tua', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function prestasiPelanggaran(): HasMany
    {
        return $this->hasMany(PrestasiPelanggaran::class, 'siswa_id');
    }
}
