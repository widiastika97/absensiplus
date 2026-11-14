<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'karyawan_id',
        'lokasi_id',
        'tanggal',
        'tipe',
        'jam',
        'foto_selfie',
        'latitude',
        'longitude',
        'jarak_meter',
        'radius_diizinkan',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'jarak_meter' => 'float',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(LokasiKantor::class, 'lokasi_id');
    }
}
