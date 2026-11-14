<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestasiPelanggaran extends Model
{
    protected $table = 'prestasi_pelanggaran';

    protected $fillable = [
        'kategori',
        'subjek',
        'siswa_id',
        'guru_id',
        'nama_manual',
        'judul',
        'keterangan',
        'foto_bukti',
        'tanggal',
        'pelapor_id',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'guru_id');
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'pelapor_id');
    }

    /**
     * Nama subjek yang ditampilkan: guru, siswa, atau input manual.
     */
    public function namaSubjek(): string
    {
        return $this->guru?->nama
            ?? $this->siswa?->nama
            ?? $this->nama_manual
            ?? '-';
    }
}
