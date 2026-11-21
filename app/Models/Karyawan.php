<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Karyawan extends Authenticatable
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'nip',
        'nama',
        'email',
        'password',
        'departemen',
        'jabatan',
        'role_id',
        'agama',
        'no_hp',
        'foto_profil',
        'jam_masuk',
        'jam_pulang',
        'kuota_cuti_tahunan',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kuota_cuti_tahunan' => 'integer',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'karyawan_id');
    }

    public function cutiKaryawan(): HasMany
    {
        return $this->hasMany(CutiKaryawan::class, 'karyawan_id');
    }

    public function pengajuanLainnya(): HasMany
    {
        return $this->hasMany(PengajuanLainnya::class, 'karyawan_id');
    }

    public function jadwalShift(): HasMany
    {
        return $this->hasMany(JadwalShiftKaryawan::class, 'karyawan_id');
    }

    public function isAdmin(): bool
    {
        return (int) $this->role_id === 1
            || $this->role?->hasPermission('admin_data_karyawan')
            || $this->role?->hasPermission('admin_persetujuan_cuti')
            || $this->role?->hasPermission('admin_titik_kantor');
    }

    public function hasPermission(string $key): bool
    {
        // Super admin (role_id = 1) memiliki akses penuh
        if ((int) $this->role_id === 1) {
            return true;
        }

        return $this->role?->hasPermission($key) ?? false;
    }

    /**
     * Kode role jenis karyawan (Tetap/Kontrak/Honor).
     */
    public function kodeRoleJenis(): ?string
    {
        $kode = $this->role?->kode_role;

        return array_key_exists($kode, RoleSeeder::JAM_KERJA_JENIS) ? $kode : null;
    }

    /**
     * Jam kerja efektif sesuai tipe (via role); NULL jika custom.
     */
    public function jamKerjaJenis(): ?array
    {
        return Pengaturan::jamKerjaJenis($this->kodeRoleJenis());
    }

    /**
     * Kuota cuti: total, terpakai (approved), pending, sisa untuk satu tahun.
     */
    public function rekapKuotaCuti(int $tahun): array
    {
        $rows = $this->cutiKaryawan()
            ->whereYear('tanggal_mulai', $tahun)
            ->get(['potong_kuota_tahunan', 'status', 'total_hari']);

        $usedAnnual = $pendingAnnual = $usedSpecial = 0;

        foreach ($rows as $l) {
            if ($l->potong_kuota_tahunan) {
                if ($l->status === 'approved') {
                    $usedAnnual += (int) $l->total_hari;
                } elseif ($l->status === 'pending') {
                    $pendingAnnual += (int) $l->total_hari;
                }
            } elseif ($l->status === 'approved') {
                $usedSpecial += (int) $l->total_hari;
            }
        }

        $total = (int) $this->kuota_cuti_tahunan;

        return [
            'total_quota' => $total,
            'used_quota' => $usedAnnual,
            'pending_quota' => $pendingAnnual,
            'remaining_quota' => max(0, $total - $usedAnnual),
            'special_days' => $usedSpecial,
        ];
    }

    /**
     * Roster shift hari ini; fallback default 08:00-17:00 (Minggu = off).
     */
    public function shiftHariIni(string $tanggal): ?JadwalShiftKaryawan
    {
        return $this->jadwalShift()
            ->where('tanggal', $tanggal)
            ->with('shift')
            ->first();
    }

    /**
     * Jendela waktu tombol Presensi Masuk aktif:
     * dari 1 jam sebelum jam masuk sampai jam pulang.
     * Mengembalikan [Carbon $mulai, Carbon $selesai].
     */
    public function jendelaPresensiMasuk(string $tanggal): array
    {
        $roster = $this->shiftHariIni($tanggal);

        if ($roster && $roster->shift) {
            $jamMasuk  = $roster->shift->jam_masuk;
            $jamPulang = $roster->shift->jam_pulang;
        } else {
            // fallback: jam custom karyawan -> default 08:00-17:00
            $jamMasuk  = $this->jam_masuk  ?: '08:00';
            $jamPulang = $this->jam_pulang ?: '17:00';
        }

        $masuk  = Carbon::parse($tanggal . ' ' . $jamMasuk);
        $pulang = Carbon::parse($tanggal . ' ' . $jamPulang);

        // Shift malam (pulang melewati tengah malam)
        if ($pulang->lessThanOrEqualTo($masuk)) {
            $pulang->addDay();
        }

        return [$masuk->copy()->subHour(), $pulang];
    }
}
