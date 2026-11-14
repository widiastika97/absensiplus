@extends('layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
@include('layouts.karyawan-header')

<main class="max-w-6xl mx-auto px-3 sm:px-4 py-4 sm:py-6 pb-28 sm:pb-6 space-y-4 sm:space-y-6">
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2.5">
            <i class="fa-solid fa-clock-rotate-left text-blue-600"></i> Riwayat Presensi Saya
        </h1>

        <!-- KPI -->
        <div class="grid grid-cols-3 gap-3 mt-4">
            <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-center">
                <p class="text-2xl font-black text-blue-700">{{ $kpi['total'] }}</p>
                <p class="text-[10px] font-bold text-blue-600 tracking-wide">TOTAL LOG</p>
            </div>
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-center">
                <p class="text-2xl font-black text-emerald-700">{{ $kpi['tepat_waktu'] }}</p>
                <p class="text-[10px] font-bold text-emerald-600 tracking-wide">TEPAT WAKTU</p>
            </div>
            <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-center">
                <p class="text-2xl font-black text-amber-700">{{ $kpi['terlambat'] }}</p>
                <p class="text-[10px] font-bold text-amber-600 tracking-wide">TERLAMBAT</p>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('riwayat') }}" class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <option value="">Semua Status</option>
                <option value="tepat_waktu" {{ ($filters['status'] ?? '') === 'tepat_waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                <option value="terlambat" {{ ($filters['status'] ?? '') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
            </select>
            <button type="submit" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold">
                <i class="fa-solid fa-filter"></i> Terapkan
            </button>
        </form>

        <div class="mt-3 flex flex-wrap gap-1.5 text-xs">
            @foreach(['hari_ini' => 'Hari Ini', '7_hari' => '7 Hari', '30_hari' => '30 Hari', 'bulan_ini' => 'Bulan Ini', 'semua' => 'Semua'] as $key => $label)
                <a href="{{ route('riwayat', ['preset' => $key]) }}"
                   class="px-3 py-1.5 rounded-xl transition font-semibold {{ $preset === $key ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @forelse($grouped as $tanggal => $logs)
            @php $masuk = $logs->firstWhere('tipe', 'masuk'); $pulang = $logs->firstWhere('tipe', 'pulang'); @endphp
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between hover:bg-slate-50/60">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
                    <p class="text-[11px] text-slate-400 font-mono">{{ $tanggal }}</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Masuk</span>
                        @if($masuk)
                            <p class="font-mono font-bold text-base {{ $masuk->status === 'terlambat' ? 'text-amber-600' : 'text-emerald-700' }}">{{ substr($masuk->jam, 0, 5) }}</p>
                            <p class="text-[10px] text-slate-400">{{ $masuk->lokasi?->nama_lokasi ?? '-' }} &bull; {{ $masuk->jarak_meter }}m</p>
                        @else
                            <p class="text-slate-300 font-bold">-</p>
                        @endif
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Pulang</span>
                        @if($pulang)
                            <p class="font-mono font-bold text-base text-blue-700">{{ substr($pulang->jam, 0, 5) }}</p>
                        @else
                            <p class="text-slate-300 font-bold">-</p>
                        @endif
                    </div>
                    @if($masuk && $masuk->foto_selfie)
                        <img src="{{ asset('storage/selfie/' . $masuk->foto_selfie) }}"
                             onerror="this.src='https://ui-avatars.com/api/?name=?&size=64';"
                             class="w-14 h-14 rounded-xl object-cover border border-slate-200 cursor-pointer hover:opacity-80"
                             onclick="previewSelfie('{{ asset('storage/selfie/' . $masuk->foto_selfie) }}')">
                    @endif
                </div>
            </div>
        @empty
            <div class="p-10 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-inbox text-3xl mb-3 block"></i>
                Tidak ada riwayat presensi pada periode ini.
            </div>
        @endforelse
    </div>
</main>

<script>
    function previewSelfie(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Foto Selfie',
            showConfirmButton: false,
        });
    }
</script>
@endsection
