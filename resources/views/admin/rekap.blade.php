@extends('layouts.admin')

@section('title', 'Rekap Presensi')

@section('content')
@include('layouts.admin-header')

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-5 space-y-5">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-blue-600"></i> Laporan Rekap Bulanan Presensi
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Periode: {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}@if($dept !== 'all') &bull; Departemen {{ $dept }}@endif</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.export.excel', ['bulan' => $bulan, 'tahun' => $tahun, 'dept' => $dept]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold cursor-pointer">
                <i class="fa-solid fa-file-excel"></i> Unduh Excel
            </a>
            <a href="{{ route('admin.export.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'dept' => $dept]) }}" target="_blank" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold cursor-pointer">
                <i class="fa-solid fa-file-pdf"></i> Unduh PDF
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold cursor-pointer">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="p-3">Tanggal & Jam</th>
                        <th class="p-3">Karyawan</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3">Lokasi Kantor</th>
                        <th class="p-3">Jarak</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Catatan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($rekap as $row)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-3 font-mono">{{ $row->tanggal->toDateString() }} {{ substr($row->jam, 0, 8) }}</td>
                        <td class="p-3">
                            <div class="font-bold">{{ $row->karyawan?->nama }}</div>
                            <div class="text-slate-400">{{ $row->karyawan?->nip }} &bull; {{ $row->karyawan?->departemen }}</div>
                        </td>
                        <td class="p-3 font-bold {{ $row->tipe === 'masuk' ? 'text-emerald-600' : 'text-blue-600' }}">{{ strtoupper($row->tipe) }}</td>
                        <td class="p-3">{{ $row->lokasi?->nama_lokasi }}</td>
                        <td class="p-3 font-mono">{{ $row->jarak_meter }} m</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $row->status === 'tepat_waktu' ? 'bg-emerald-500/20 text-emerald-700' : 'bg-amber-500/20 text-amber-700' }}">{{ strtoupper($row->status) }}</span>
                        </td>
                        @php $catatanRow = $catatanMap->get($row->karyawan_id.'_'.$row->tanggal->toDateString()); @endphp
                        <td class="p-3">
                            @if($catatanRow)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gradient-to-r from-amber-100 to-yellow-50 border border-amber-200/70 text-[10px] font-semibold text-amber-700 shadow-sm" title="Dicatat oleh {{ $catatanRow->admin_name }}">
                                <i class="fa-solid fa-file-lines"></i> {{ $catatanRow->catatan }}
                            </span>
                            @else
                            <span class="text-slate-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="p-10 text-center text-slate-400 italic">Tidak ada data presensi pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
