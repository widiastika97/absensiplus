<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalLibur extends Model
{
    protected $table = 'jadwal_libur';

    protected $fillable = [
        'nama_libur',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe_target',
        'target_agama',
        'target_karyawan_ids',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'target_karyawan_ids' => 'array',
    ];

    /**
     * Apakah hari libur ini berlaku untuk karyawan tertentu
     * (target semua / per agama / per karyawan).
     */
    public function berlakuUntuk(Karyawan $karyawan): bool
    {
        return match ($this->tipe_target) {
            'semua' => true,
            'agama' => $this->target_agama === $karyawan->agama,
            'karyawan' => in_array($karyawan->id, $this->target_karyawan_ids ?? []),
            default => false,
        };
    }
}
