<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterShift extends Model
{
    protected $table = 'master_shift';

    protected $fillable = [
        'kode_shift',
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
        'is_night_shift',
        'is_off',
        'warna_badge',
        'deskripsi',
    ];

    protected $casts = [
        'is_night_shift' => 'boolean',
        'is_off' => 'boolean',
    ];

    public function jadwalKaryawan(): BelongsTo
    {
        return $this->hasMany(JadwalShiftKaryawan::class, 'shift_id');
    }
}
