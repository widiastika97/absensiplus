<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CutiKaryawan;
use App\Models\JadwalLibur;
use App\Models\Karyawan;
use App\Models\LokasiKantor;
use App\Models\Presensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Seeder dummy absensi 30 hari terakhir untuk pengujian rekap (porting dari admin/seeder_absensi.php).
 */
class SeederAbsensiController extends Controller
{
    public function __invoke(Request $request)
    {
        $days = max(7, min(90, (int) $request->query('days', 30)));
        $truncate = (int) $request->query('truncate', 1);

        $startDate = now()->subDays($days - 1)->toDateString();
        $endDate = now()->toDateString();

        $karyawanList = Karyawan::where('is_active', 1)->orderBy('id')->get();
        $lokasiList = LokasiKantor::where('is_active', 1)->orderBy('id')->get();

        if ($karyawanList->isEmpty() || $lokasiList->isEmpty()) {
            $message = 'Master karyawan atau lokasi kantor masih kosong.';

            return $request->wantsJson()
                ? new JsonResponse(['success' => false, 'message' => $message], 422)
                : back()->with('error', $message);
        }

        $defaultLokasi = $lokasiList->first();

        if ($truncate) {
            Presensi::whereBetween('tanggal', [$startDate, $endDate])->delete();
        }

        // Cuti approved dalam rentang
        $cutiMap = [];
        CutiKaryawan::where('status', 'approved')
            ->whereDate('tanggal_mulai', '<=', $endDate)
            ->whereDate('tanggal_selesai', '>=', $startDate)
            ->get(['karyawan_id', 'tanggal_mulai', 'tanggal_selesai', 'label_tipe'])
            ->each(function ($c) use (&$cutiMap, $startDate, $endDate) {
                $cur = new \DateTime(max($c->tanggal_mulai->toDateString(), $startDate));
                $end = new \DateTime(min($c->tanggal_selesai->toDateString(), $endDate));
                while ($cur <= $end) {
                    $cutiMap[$c->karyawan_id][$cur->format('Y-m-d')] = $c->label_tipe ?: 'Cuti';
                    $cur->modify('+1 day');
                }
            });

        // Libur nasional dalam rentang
        $liburMap = [];
        JadwalLibur::whereDate('tanggal_mulai', '<=', $endDate)
            ->whereDate('tanggal_selesai', '>=', $startDate)
            ->get(['tanggal_mulai', 'tanggal_selesai', 'nama_libur'])
            ->each(function ($l) use (&$liburMap, $startDate, $endDate) {
                $cur = new \DateTime(max($l->tanggal_mulai->toDateString(), $startDate));
                $end = new \DateTime(min($l->tanggal_selesai->toDateString(), $endDate));
                while ($cur <= $end) {
                    $liburMap[$cur->format('Y-m-d')] = $l->nama_libur;
                    $cur->modify('+1 day');
                }
            });

        $rows = [];
        $totalInserted = 0;

        for ($i = $days - 1; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->toDateString();
            $dayOfWeek = (int) now()->subDays($i)->format('N');

            // Lewati Minggu & libur nasional
            if ($dayOfWeek === 7 || isset($liburMap[$tanggal])) {
                continue;
            }

            foreach ($karyawanList as $kIndex => $karyawan) {
                // Lewati jika sedang cuti approved
                if (isset($cutiMap[$karyawan->id][$tanggal])) {
                    continue;
                }

                // Simulasi sesekali alpha (karyawan index 2 pada interval 7)
                if ($kIndex === 2 && $i === 7) {
                    continue;
                }

                $lokasi = ($lokasiList->count() > 1 && $kIndex === 2) ? $lokasiList[1] : $defaultLokasi;

                $isLate = (($i * 3 + $kIndex * 7) % 11 === 1);
                $isOvertime = (($i * 5 + $kIndex * 2) % 9 === 0);

                // Presensi masuk
                $jamMasuk = sprintf('08:%02d:%02d', 2 + ($kIndex % 10), ($i * 7) % 60);
                $statusMasuk = 'tepat_waktu';
                $ketMasuk = 'Hadir tepat waktu di lokasi kantor';

                if ($isLate) {
                    $jamMasuk = sprintf('08:%02d:%02d', 20 + ($i % 25), ($i * 11) % 60);
                    $statusMasuk = 'terlambat';
                    $ketMasuk = 'Terlambat akibat kendala kemacetan lalu lintas';
                }

                $jitterLat = (($i % 5) - 2) * 0.00005;
                $jitterLng = ((($i + $kIndex) % 5) - 2) * 0.00005;
                $latIn = (float) $lokasi->latitude + $jitterLat;
                $lngIn = (float) $lokasi->longitude + $jitterLng;
                $jarakIn = round(12 + (($i + $kIndex * 5) % max(15, ($lokasi->radius - 20))), 2);

                $rows[] = [
                    'karyawan_id' => $karyawan->id,
                    'lokasi_id' => $lokasi->id,
                    'tanggal' => $tanggal,
                    'tipe' => 'masuk',
                    'jam' => $jamMasuk,
                    'foto_selfie' => 'seeded selfies are not generated',
                    'latitude' => $latIn,
                    'longitude' => $lngIn,
                    'jarak_meter' => $jarakIn,
                    'radius_diizinkan' => $lokasi->radius,
                    'status' => $statusMasuk,
                    'keterangan' => $ketMasuk,
                ];
                $totalInserted++;

                // Presensi pulang
                if ($i > 0 || ($i === 0 && $kIndex <= 1)) {
                    $jamPulang = sprintf('17:%02d:%02d', 10 + ($kIndex % 20), ($i * 13) % 60);
                    $statusPulang = 'tepat_waktu';
                    $ketPulang = 'Absen pulang reguler';

                    if ($isOvertime) {
                        $jamPulang = sprintf('18:%02d:%02d', 30 + ($i % 28), ($i * 9) % 60);
                        $statusPulang = 'lembur';
                        $ketPulang = 'Lembur menyelesaikan rekapitulasi data pekerjaan';
                    }

                    $jarakOut = round(10 + (($i * 2 + $kIndex * 3) % max(15, ($lokasi->radius - 22))), 2);

                    $rows[] = [
                        'karyawan_id' => $karyawan->id,
                        'lokasi_id' => $lokasi->id,
                        'tanggal' => $tanggal,
                        'tipe' => 'pulang',
                        'jam' => $jamPulang,
                        'foto_selfie' => 'seeded selfies are not generated',
                        'latitude' => $latIn,
                        'longitude' => $lngIn,
                        'jarak_meter' => $jarakOut,
                        'radius_diizinkan' => $lokasi->radius,
                        'status' => $statusPulang,
                        'keterangan' => $ketPulang,
                    ];
                    $totalInserted++;
                }
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            foreach ($chunk as &$row) {
                $row['created_at'] = now();
                $row['updated_at'] = now();
            }
            Presensi::insert($chunk);
        }

        $message = "Berhasil meng-generate {$totalInserted} data presensi dummy untuk ".$karyawanList->count().' karyawan periode '.$startDate.' s/d '.$endDate.'.';

        return $request->wantsJson()
            ? response()->json(['success' => true, 'message' => $message, 'total_inserted' => $totalInserted, 'total_karyawan' => $karyawanList->count(), 'start_date' => $startDate, 'end_date' => $endDate])
            : back()->with('success', $message);
    }
}
