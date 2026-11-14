@extends('layouts.app')

@section('title', ucfirst($kategori) . ' - Absenplus+')
@php $isPrestasi = $kategori === 'prestasi'; $accent = $isPrestasi ? 'emerald' : 'rose'; $icon = $isPrestasi ? 'fa-trophy' : 'fa-triangle-exclamation'; @endphp

@push('head')
<style>
input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; }
</style>
@endpush

@section('content')
@include('layouts.karyawan-header')

<main class="max-w-6xl mx-auto px-2.5 sm:px-4 py-4 sm:py-6 space-y-4 sm:space-y-6">

    <!-- Tab Prestasi / Pelanggaran -->
    <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-sm flex gap-1 text-xs font-semibold">
        <a href="{{ route('prestasi') }}" @class(['px-4 py-2 rounded-xl flex-1 text-center transition',
            $isPrestasi ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100'])>
            <i class="fa-solid fa-trophy"></i> Prestasi
        </a>
        <a href="{{ route('pelanggaran') }}" @class(['px-4 py-2 rounded-xl flex-1 text-center transition',
            !$isPrestasi ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100'])>
            <i class="fa-solid fa-triangle-exclamation"></i> Pelanggaran
        </a>
    </div>

    <a href="{{ route('absen') }}#panel-catatan" class="block p-3 rounded-2xl border border-slate-200 bg-white shadow-sm text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
        <i class="fa-solid fa-circle-plus text-{{ $accent }}-600"></i>
        Untuk menambah {{ $kategori }} baru, buka halaman <span class="text-blue-600 font-bold">Absen</span> lalu pilih "Tambah {{ ucfirst($kategori) }}".
        <i class="fa-solid fa-arrow-right text-slate-400"></i>
    </a>

    <!-- Filter -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-3">
        <form method="GET" class="contents">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase">Cari Nama/Keterangan</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari..." class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase">Subjek</label>
                <select name="tipe_subjek" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                    <option value="">Semua</option>
                    <option value="siswa" {{ request('tipe_subjek') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    @if($isAdmin)<option value="guru" {{ request('tipe_subjek') === 'guru' ? 'selected' : '' }}>Guru</option>@endif
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div class="col-span-2 sm:col-span-4">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-filter"></i> Terapkan Filter</button>
                <a href="{{ route($isPrestasi ? 'prestasi' : 'pelanggaran') }}" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold">Reset</a>
            </div>
        </form>
    </div>

    <!-- Daftar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900">Daftar {{ ucfirst($kategori) }} <span class="text-slate-400">({{ $dataList->count() }})</span></h3>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($dataList as $item)
            <div class="p-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->subjek === 'guru' ? 'bg-purple-500/20 text-purple-700' : 'bg-slate-500/10 text-slate-600' }}">{{ strtoupper($item->subjek) }}</span>
                        <span class="text-xs font-bold text-slate-900 truncate">{{ $item->namaSubjek() }}</span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $item->tanggal->translatedFormat('d M Y') }}</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-700 mt-1">{{ $item->judul }}</p>
                    @if($item->keterangan)
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $item->keterangan }}</p>
                    @endif
                    @if($item->foto_bukti)
                    <a href="{{ Storage::url($item->foto_bukti) }}" target="_blank" class="inline-block mt-1.5">
                        <img src="{{ Storage::url($item->foto_bukti) }}" class="h-16 w-24 object-cover rounded-lg border border-slate-200" alt="Bukti">
                    </a>
                    @endif
                    <p class="text-[10px] text-slate-400 mt-1"><i class="fa-solid fa-pen-nib"></i> Dilaporkan oleh: {{ $item->pelapor?->nama ?? '-' }}</p>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-xs text-slate-400">
                <i class="fa-solid fa-inbox text-2xl mb-2 block text-slate-200"></i> Belum ada data {{ $kategori }}.
            </div>
            @endforelse
        </div>
    </div>
</main>
@endsection

@push('scripts')
@endpush
