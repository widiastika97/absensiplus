<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanLainnya extends Model
{
    protected $table = 'pengajuan_lainnya';

    protected $fillable = [
        'karyawan_id',
        'tipe_pengajuan',
        'label_tipe',
        'tanggal',
        'jam_estimasi',
        'target_approver_id',
        'target_approver_name',
        'alasan',
        'foto_bukti',
        'status',
        'approved_by_id',
        'approved_by_name',
        'approved_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal' => 'date',
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
}
