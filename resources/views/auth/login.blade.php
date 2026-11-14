@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 px-4">
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-2xl p-7 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 mx-auto bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg shadow-blue-600/30">
                <i class="fa-solid fa-fingerprint"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Absenplus+</h1>
            <p class="text-xs text-slate-500">Sistem Absensi Karyawan &bull; Geofencing & Swafoto</p>
        </div>

        @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold text-slate-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $rememberedEmail) }}" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="space-y-1.5">
                <label for="password" class="text-xs font-bold text-slate-700">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <label class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                <input type="checkbox" name="remember_me" {{ !empty($rememberedEmail) ? 'checked' : '' }} class="rounded text-blue-600">
                Ingat Saya
            </label>

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-600/20 transition active:scale-98">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div class="pt-3 border-t border-slate-100 text-center text-[10px] text-slate-400">
            Pastikan izin Kamera dan Lokasi (GPS) diaktifkan pada browser Anda
        </div>
    </div>
</div>
@endsection
