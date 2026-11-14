<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LokasiKantor extends Model
{
    protected $table = 'lokasi_kantor';

    protected $fillable = [
        'kode_lokasi',
        'nama_lokasi',
        'alamat',
        'kota',
        'latitude',
        'longitude',
        'radius',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius' => 'integer',
        'is_active' => 'boolean',
    ];

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'lokasi_id');
    }

    /**
     * Formula Haversine: jarak (meter) dari koordinat user ke titik kantor.
     */
    public function jarakDari(float $lat, float $lng): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat - (float) $this->latitude);
        $dLng = deg2rad($lng - (float) $this->longitude);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad((float) $this->latitude)) * cos(deg2rad($lat)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }
}
