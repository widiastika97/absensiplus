<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanAbsensi extends Model
{
    protected $table = 'catatan_absensi';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'catatan',
        'admin_id',
        'admin_name',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
