<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(Request $request): View
    {
        if (Auth::guard('karyawan')->check()) {
            return redirect($this->redirectBasedOnRole(Auth::guard('karyawan')->user()));
        }

        return view('auth.login', [
            'rememberedEmail' => $request->cookie('remember_email', ''),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email dan kata sandi wajib diisi!',
            'password.required' => 'Email dan kata sandi wajib diisi!',
        ]);

        $user = Karyawan::where('email', $credentials['email'])
            ->where('is_active', 1)
            ->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'Akun dengan email tersebut tidak ditemukan atau status nonaktif.',
            ])->onlyInput('email');
        }

        // Fallback migrasi: dukung hash plaintext/MD5 lama lalu upgrade otomatis ke Bcrypt
        $isLegacyPlaintext = $user->password === $credentials['password'];
        $isLegacyMd5 = md5($credentials['password']) === $user->password;

        $authenticated = Auth::guard('karyawan')->attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            false
        );

        if (! $authenticated && ! $isLegacyPlaintext && ! $isLegacyMd5) {
            return back()->withErrors([
                'password' => 'Kata sandi salah! Silakan periksa kembali kata sandi Anda.',
            ])->onlyInput('email');
        }

        if (! $authenticated && ($isLegacyPlaintext || $isLegacyMd5)) {
            $user->password = $credentials['password'];
            $user->save();

            Auth::guard('karyawan')->login($user);
        }

        $request->session()->regenerate();

        // Cookie "Ingat Saya" 30 hari (hanya mengingat email)
        if ($request->boolean('remember_me')) {
            cookie()->queue('remember_email', $credentials['email'], 60 * 24 * 30);
        } else {
            cookie()->queue(cookie()->forget('remember_email'));
        }

        return redirect()->intended($this->redirectBasedOnRole($user));
    }

    public function logout(Request $request)
    {
        Auth::guard('karyawan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectBasedOnRole(Karyawan $user): string
    {
        return $user->isAdmin() || (int) $user->role_id === 1
            ? route('admin.dashboard')
            : route('absen');
    }
}
