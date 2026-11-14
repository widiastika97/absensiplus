@extends('layouts.app')

@section('title', 'Laporan Prestasi & Pelanggaran - Admin')

@section('content')
@include('layouts.karyawan-header')

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-5 pb-28 sm:pb-5 space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-slate-900">Laporan Prestasi &amp; Pelanggaran</h1>
                <p class="text-[10px] text-slate-400">Periode: <strong class="text-slate-600">{{ \Illuminate\Support\Carbon::parse($dari)->isoFormat('D MMM Y') }} — {{ \Illuminate\Support\Carbon::parse($sampai)->isoFormat('D MMM Y') }}</strong></p>
            </div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer transition">
            <i class="fa-solid fa-gauge text-slate-500"></i> Dashboard Utama
        </a>
    </div>
    
    @include('admin.partials.laporan-prestasi-content')
@endsection
