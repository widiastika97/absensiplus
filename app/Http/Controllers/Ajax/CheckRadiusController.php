<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use App\Services\GeofenceService;
use Illuminate\Http\Request;

class CheckRadiusController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ], [
            'latitude.required' => 'Koordinat GPS wajib dikirim',
            'longitude.required' => 'Koordinat GPS wajib dikirim',
        ]);

        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        $lokasiList = LokasiKantor::where('is_active', 1)->get();

        if ($lokasiList->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Belum ada titik lokasi kantor aktif']);
        }

        [, , $kantorTerdekat, $jarakTerdekat] = GeofenceService::findValidOffice($lat, $lng);

        $isDalamRadius = false;
        $hasilLokasi = [];
        $kantorTerpilih = null;

        foreach ($lokasiList as $lokasi) {
            $jarak = $lokasi->jarakDari($lat, $lng);
            $validRadius = $jarak <= $lokasi->radius;

            $info = [
                'id' => $lokasi->id,
                'nama_lokasi' => $lokasi->nama_lokasi,
                'latitude' => $lokasi->latitude,
                'longitude' => $lokasi->longitude,
                'radius' => $lokasi->radius,
                'jarak_meter' => $jarak,
                'is_valid' => $validRadius,
            ];

            $hasilLokasi[] = $info;

            if ($validRadius) {
                $isDalamRadius = true;
                $kantorTerpilih = $info;
            }
        }

        if (! $kantorTerpilih && $kantorTerdekat) {
            $kantorTerpilih = [
                'id' => $kantorTerdekat->id,
                'nama_lokasi' => $kantorTerdekat->nama_lokasi,
                'latitude' => $kantorTerdekat->latitude,
                'longitude' => $kantorTerdekat->longitude,
                'radius' => $kantorTerdekat->radius,
                'jarak_meter' => $jarakTerdekat,
                'is_valid' => false,
            ];
        }

        return response()->json([
            'status' => 'success',
            'is_within_radius' => $isDalamRadius,
            'current_location' => $kantorTerpilih,
            'distance_meters' => $kantorTerpilih['jarak_meter'] ?? 0,
            'allowed_radius' => $kantorTerpilih['radius'] ?? 0,
            'all_locations' => $hasilLokasi,
        ]);
    }
}
