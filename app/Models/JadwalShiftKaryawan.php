<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalShiftKaryawan extends Model
{
    protected $table = 'jadwal_shift_karyawan';

    protected $fillable = [
        'karyawan_id',
        'shift_id',
        'tanggal',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(MasterShift::class, 'shift_id');
    }
}
