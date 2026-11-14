<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiKaryawan extends Model
{
    protected $table = 'cuti_karyawan';

    protected $fillable = [
        'karyawan_id',
        'target_approver_id',
        'target_approver_name',
        'kategori',
        'tipe_cuti',
        'label_tipe',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_hari',
        'potong_kuota_tahunan',
        'alasan',
        'lampiran_dokumen',
        'status',
        'approved_by_id',
        'approved_by_name',
        'approved_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'potong_kuota_tahunan' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'target_approver_id');
    }

    /**
     * Mapping tipe cuti ke kategori, label, dan aturan kuota (sama seperti app lama).
     */
    public static function tipeConfig(): array
    {
        return [
            'tahunan' => ['kategori' => 'tahunan', 'label' => 'Cuti Tahunan Pribadi', 'potong_kuota' => true],
            'sakit' => ['kategori' => 'khusus', 'label' => 'Cuti Sakit (Surat Dokter)', 'potong_kuota' => false],
            'melahirkan' => ['kategori' => 'khusus', 'label' => 'Cuti Melahirkan (Maternity)', 'potong_kuota' => false],
            'izin' => ['kategori' => 'khusus', 'label' => 'Izin Urusan Pribadi / Keluarga', 'potong_kuota' => false],
        ];
    }

    /**
     * Hitung total hari kerja efektif (mengabaikan hari Minggu).
     */
    public static function hitungTotalHari(string $mulai, string $selesai): int
    {
        $total = 0;
        $curr = new \DateTime($mulai);
        $end = new \DateTime($selesai);

        while ($curr <= $end) {
            if ((int) $curr->format('N') !== 7) { // 7 = Minggu
                $total++;
            }
            $curr->modify('+1 day');
        }

        return max(1, $total);
    }
}
