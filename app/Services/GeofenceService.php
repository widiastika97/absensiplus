<?php

namespace App\Services;

use App\Models\LokasiKantor;

class GeofenceService
{
    /**
     * Temukan kantor valid berdasarkan koordinat GPS user.
     * Mengembalikan [lokasi | null, jarak lokasi valid, lokasi terdekat, jarak terdekat].
     */
    public static function findValidOffice(float $lat, float $lng): array
    {
        $lokasiList = LokasiKantor::where('is_active', 1)->get();

        $kantorValid = null;
        $jarakTervalidasi = 0;
        $jarakTerdekat = PHP_FLOAT_MAX;
        $kantorTerdekat = null;

        foreach ($lokasiList as $lok) {
            $jarak = $lok->jarakDari($lat, $lng);

            if ($jarak <= $lok->radius) {
                $kantorValid = $lok;
                $jarakTervalidasi = $jarak;
                break;
            }

            if ($jarak < $jarakTerdekat) {
                $jarakTerdekat = $jarak;
                $kantorTerdekat = $lok;
            }
        }

        return [$kantorValid, $jarakTervalidasi, $kantorTerdekat, $jarakTerdekat];
    }
}
