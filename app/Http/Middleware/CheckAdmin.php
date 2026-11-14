<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware "admin": user harus punya setidaknya satu izin modul admin.
 */
class CheckAdmin
{
    private const ADMIN_PERMISSIONS = [
        'admin_data_karyawan',
        'admin_titik_kantor',
        'admin_manajemen_role',
        'admin_persetujuan_cuti',
        'admin_jadwal_libur',
        'admin_log_presensi',
        'admin_rekap_ekspor',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('karyawan');

        if (! $user) {
            return redirect()->route('login');
        }

        if ((int) $user->role_id === 1) {
            return $next($request);
        }

        foreach (self::ADMIN_PERMISSIONS as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        return redirect()->route('absen')->with('error', 'akses_ditolak');
    }
}
