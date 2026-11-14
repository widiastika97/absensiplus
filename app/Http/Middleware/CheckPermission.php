<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RBAC: batasi akses route berdasarkan izin fitur (kolom izin_akses di tabel roles).
 * Super admin (role_id = 1) selalu lolos.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user('karyawan');

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }

            return redirect()->route('login');
        }

        if ((int) $user->role_id === 1) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk aksi ini.'], 403);
        }

        abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
    }
}
