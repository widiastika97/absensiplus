@extends('layouts.admin')

@push('head')
<style>
    /* Pastikan warna & catatan tercetak saat "Cetak Rekap" */
    @media print {
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @page { size: A4 landscape; margin: 8mm; }
    }
</style>
@endpush

@section('title', 'Kalender Detail Absensi - ' . $karyawan->nama . ' (' . \Carbon\Carbon::create(null, $bulan)->translatedFormat('F Y') . ')')

@php
    // Nama bulan Indonesia (dari app asli)
    $namaBulanIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    // Statistik kehadiran bulan ini (porting dari logika asli)
    $todayStr = now()->toDateString();
    $totalHadir = 0; $totalTepatWaktu = 0; $totalTerlambat = 0; $totalCuti = 0; $totalLibur = 0;
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $curDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
        if ($curDate > $todayStr) continue;
        $dayOfWeek = (int) date('N', strtotime($curDate));
        $isWeekendDay = ($dayOfWeek == 7);
        $liburTgl = $liburList->first(fn ($l) => $curDate >= $l->tanggal_mulai->toDateString() && $curDate <= $l->tanggal_selesai->toDateString());
        $cutiTgl = $cutiList->first(fn ($c) => $curDate >= $c->tanggal_mulai->toDateString() && $curDate <= $c->tanggal_selesai->toDateString());
        $rosterTgl = $rosterMap->get($curDate);
        $masukHari = $presensi->get($curDate)?->firstWhere('tipe', 'masuk');
        if ($masukHari) {
            $totalHadir++;
            if ($masukHari->status === 'tepat_waktu') $totalTepatWaktu++;
            else $totalTerlambat++;
        } elseif ($cutiTgl) {
            $totalCuti++;
        } elseif ($liburTgl || ($rosterTgl && $rosterTgl->shift->is_off) || ($isWeekendDay && !$rosterTgl)) {
            $totalLibur++;
        }
    }
@endphp

@section('content')
{{-- Body class khusus halaman ini mengikuti app asli --}}

<!-- Top Navigation Bar -->
<header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="p-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl text-slate-600 transition shadow-sm" title="Kembali ke Log Absensi">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-blue-600"></i> Detail Riwayat & Kalender Absensi Karyawan
                </h1>
                <p class="text-xs text-slate-500">Audit presensi swafoto, jam masuk & pulang, dan lokasi geofencing</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-gauge text-slate-500"></i> Dashboard Utama
            </a>
            <button onclick="window.print()" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm shadow-blue-500/20 cursor-pointer">
                <i class="fa-solid fa-print"></i> Cetak Rekap
            </button>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
    <!-- Profile & Filter Section -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <!-- Profil Karyawan -->
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 shadow-md overflow-hidden shrink-0 flex items-center justify-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($karyawan->nama) }}&background=0284c7&color=fff&size=128&bold=true"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($karyawan->nama) }}&background=94a3b8&color=fff&size=128';"
                     class="w-full h-full object-cover"
                     alt="{{ $karyawan->nama }}" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-extrabold text-slate-900">{{ $karyawan->nama }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold">
                        {{ $karyawan->role?->nama_role ?? '-' }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 font-mono mt-0.5 flex flex-wrap items-center gap-2">
                    <span>NIP: <strong class="text-slate-700">{{ $karyawan->nip }}</strong></span>
                    <span>&bull;</span>
                    <span>Dept: <strong class="text-slate-700">{{ $karyawan->departemen ?? '-' }}</strong></span>
                    <span>&bull;</span>
                    <span>Jabatan: <strong class="text-slate-700">{{ $karyawan->jabatan ?? '-' }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Form Pilih Karyawan, Bulan & Tahun -->
        <form method="GET" class="flex flex-wrap items-center gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-200">
            <select name="karyawan_id" onchange="this.form.submit()" class="bg-white border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                @foreach($allKaryawan as $k)
                <option value="{{ $k->id }}" {{ $k->id == $karyawan->id ? 'selected' : '' }}>
                    {{ $k->nama }} ({{ $k->nip }})
                </option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="bg-white border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>{{ $namaBulanIndo[$m] }}</option>
                @endfor
            </select>
            <select name="tahun" onchange="this.form.submit()" class="bg-white border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                @for($y = 2024; $y <= 2028; $y++)
                <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                <i class="fa-solid fa-filter"></i>
            </button>
        </form>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[10px] uppercase tracking-wider font-bold">Total Hadir</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs"><i class="fa-solid fa-circle-check"></i></span>
            </div>
            <div class="text-2xl font-black text-slate-900 font-mono mt-1">{{ $totalHadir }} Hari</div>
            <div class="text-[11px] text-emerald-600 font-medium mt-0.5">Bulan {{ $namaBulanIndo[$bulan] }}</div>
        </div>
        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[10px] uppercase tracking-wider font-bold">Tepat Waktu</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs"><i class="fa-solid fa-clock"></i></span>
            </div>
            <div class="text-2xl font-black text-blue-600 font-mono mt-1">{{ $totalTepatWaktu }} Hari</div>
            <div class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $totalHadir > 0 ? round(($totalTepatWaktu / $totalHadir) * 100) : 0 }}% Rasio Tepat Waktu</div>
        </div>
        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[10px] uppercase tracking-wider font-bold">Terlambat</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs"><i class="fa-solid fa-triangle-exclamation"></i></span>
            </div>
            <div class="text-2xl font-black text-amber-600 font-mono mt-1">{{ $totalTerlambat }} Hari</div>
            <div class="text-[11px] text-amber-600 font-medium mt-0.5">Memerlukan Perhatian</div>
        </div>
        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[10px] uppercase tracking-wider font-bold">Cuti / Izin</span>
                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs"><i class="fa-solid fa-calendar-check"></i></span>
            </div>
            <div class="text-2xl font-black text-purple-600 font-mono mt-1">{{ $totalCuti }} Hari</div>
            <div class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $totalLibur }} Hari Libur/Weekend</div>
        </div>
    </div>

    <!-- KALENDER BULANAN PRESENSI INTERAKTIF -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm space-y-4 p-6">
        <!-- Calendar Navigation Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.detail-absensi', ['karyawan_id' => $karyawan->id, 'bulan' => $prev['bulan'], 'tahun' => $prev['tahun']]) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition shadow-sm" title="Bulan Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-blue-600"></i> {{ $namaBulanIndo[$bulan] }} {{ $tahun }}
                </h2>
                <a href="{{ route('admin.detail-absensi', ['karyawan_id' => $karyawan->id, 'bulan' => $next['bulan'], 'tahun' => $next['tahun']]) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition shadow-sm" title="Bulan Berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Hadir</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Terlambat</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Cuti</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Libur</span>
            </div>
        </div>

        <!-- Petunjuk Klik Tanggal -->
        <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-2xl flex items-center gap-2.5 text-xs text-blue-800">
            <i class="fa-solid fa-circle-info text-blue-600 text-sm shrink-0"></i>
            <span><strong>Tips Interaktif:</strong> Klik pada salah satu kotak tanggal untuk menampilkan popup modal detail jam masuk, jam pulang, swafoto selfie, dan koordinat GPS di hari tersebut.</span>
        </div>

        <!-- Header Nama Hari (Senin s/d Minggu) -->
        <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold text-slate-500 uppercase tracking-wider py-2 bg-slate-100/70 rounded-2xl">
            <div>Senin</div><div>Selasa</div><div>Rabu</div><div>Kamis</div><div>Jumat</div><div>Sabtu</div><div class="text-rose-500">Minggu</div>
        </div>

        <!-- Grid Kotak-Kotak Tanggal -->
        <div class="grid grid-cols-7 gap-2">
            @php $firstDayOfWeek = (int) date('N', strtotime(sprintf('%04d-%02d-01', $tahun, $bulan))); @endphp
            @for($p = 1; $p < $firstDayOfWeek; $p++)
            <div class="min-h-[105px] bg-slate-50/50 rounded-2xl border border-slate-100 p-2.5 opacity-40"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $day);
                    $dayOfWeek = (int) date('N', strtotime($dateStr));
                    $isWeekend = ($dayOfWeek == 7);
                    $isToday = ($dateStr === $todayStr);
                    $isFuture = ($dateStr > $todayStr);

                    $hadirData = $presensi->get($dateStr);
                    $logMasuk = $hadirData?->firstWhere('tipe', 'masuk');
                    $logPulang = $hadirData?->firstWhere('tipe', 'pulang');
                    $hasMasuk = !empty($logMasuk);
                    $hasPulang = !empty($logPulang);

                    $liburName = $liburList->first(fn ($l) => $dateStr >= $l->tanggal_mulai->toDateString() && $dateStr <= $l->tanggal_selesai->toDateString())?->nama_libur;
                    $cutiRow = $cutiList->first(fn ($c) => $dateStr >= $c->tanggal_mulai->toDateString() && $dateStr <= $c->tanggal_selesai->toDateString());
                    $cutiName = $cutiRow ? ($cutiRow->label_tipe ?: $cutiRow->tipe_cuti) : null;
                    $catatanAdmin = $catatanMap->get($dateStr)?->catatan;
                    // Roster karyawan menentukan hari kerja/libur (bukan asumsi hari Minggu)
                    $rosterItem = $rosterMap->get($dateStr);
                    $rosterOff = $rosterItem && $rosterItem->shift->is_off;
                    $rosterNama = $rosterItem?->shift->nama_shift;
                    $weekendLibur = $isWeekend && !$rosterItem;

                    // Tentukan style border dan background kotak
                    $cardClass = "bg-white border-slate-200 hover:border-blue-400 hover:shadow-md";
                    $badgeHtml = "";

                    if ($hasMasuk && $hasPulang) {
                        $isLate = ($logMasuk->status === 'terlambat');
                        $cardClass = $isLate ? "bg-amber-50/30 border-amber-300 hover:border-amber-500 hover:shadow-md" : "bg-emerald-50/30 border-emerald-300 hover:border-emerald-500 hover:shadow-md";
                        $badgeHtml = '<span class="px-2 py-0.5 rounded-md text-[10px] font-bold ' . ($isLate ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') . '">Hadir Lengkap</span>';
                    } elseif ($hasMasuk) {
                        $cardClass = "bg-blue-50/30 border-blue-300 hover:border-blue-500 hover:shadow-md";
                        $badgeHtml = '<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800">Masuk Saja</span>';
                    } elseif ($cutiName) {
                        $cardClass = "bg-purple-50/40 border-purple-200 hover:border-purple-400 hover:shadow-md";
                        $badgeHtml = '<span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-800 truncate block">Cuti: ' . e($cutiName) . '</span>';
                    } elseif ($liburName) {
                        $cardClass = "bg-amber-50/40 border-amber-200 hover:border-amber-400 hover:shadow-md";
                        $badgeHtml = '<span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 truncate block">Libur: ' . e($liburName) . '</span>';
                    } elseif ($rosterOff) {
                        $cardClass = "bg-amber-50/40 border-amber-200 hover:border-amber-400 hover:shadow-md";
                        $badgeHtml = '<span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 truncate block">Libur: ' . e($rosterNama) . '</span>';
                    } elseif ($weekendLibur) {
                        $cardClass = "bg-slate-100/50 border-slate-200";
                        $badgeHtml = '<span class="text-[10px] text-slate-400 italic">Akhir Pekan</span>';
                    } elseif (!$isFuture) {
                        $cardClass = "bg-rose-50/20 border-rose-200 hover:border-rose-300 hover:shadow-md";
                        $badgeHtml = '<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700">Tidak Hadir</span>';
                    } else {
                        $cardClass = "bg-white border-slate-200";
                        $badgeHtml = '<span class="text-[10px] text-slate-400">-</span>';
                    }

                    // Payload JSON untuk modal
                    $modalData = [
                        'tanggal' => $dateStr,
                        'tanggal_formatted' => \Carbon\Carbon::parse($dateStr)->translatedFormat('d F Y'),
                        'hari' => \Carbon\Carbon::parse($dateStr)->dayName,
                        'is_today' => $isToday,
                        'is_weekend' => $isWeekend,
                        'libur_name' => $liburName,
                        'cuti_name' => $cutiName,
                        'catatan_admin' => $catatanAdmin,
                        'masuk' => $logMasuk ? [
                            'jam' => $logMasuk->jam,
                            'status' => $logMasuk->status,
                            'jarak_meter' => $logMasuk->jarak_meter,
                            'foto_selfie' => $logMasuk->foto_selfie,
                            'nama_lokasi' => $logMasuk->lokasi?->nama_lokasi ?: 'Kantor',
                            'catatan' => $logMasuk->keterangan,
                        ] : null,
                        'pulang' => $logPulang ? [
                            'jam' => $logPulang->jam,
                            'status' => $logPulang->status,
                            'jarak_meter' => $logPulang->jarak_meter,
                            'foto_selfie' => $logPulang->foto_selfie,
                            'nama_lokasi' => $logPulang->lokasi?->nama_lokasi ?: 'Kantor',
                            'catatan' => $logPulang->keterangan,
                        ] : null,
                    ];
                @endphp
                <div onclick='bukaModalDetailTanggal({{ json_encode($modalData) }})' class="min-h-[110px] rounded-2xl border p-2.5 flex flex-col justify-between transition cursor-pointer group shadow-sm {{ $cardClass }} {{ $isToday ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="flex items-start justify-between">
                        <span class="w-6 h-6 rounded-lg font-mono font-bold text-xs flex items-center justify-center {{ $isToday ? 'bg-blue-600 text-white shadow-sm' : ($isWeekend ? 'text-rose-500 font-bold' : 'text-slate-700') }}">
                            {{ $day }}
                        </span>
                        @if($isToday)
                            <span class="px-1.5 py-0.2 rounded bg-blue-100 text-blue-700 text-[9px] font-bold">Hari Ini</span>
                        @endif
                    </div>

                    <div class="my-1 text-center">
                        {!! $badgeHtml !!}
                    </div>

                    @if($catatanAdmin)
                    <div class="mt-1.5 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gradient-to-r from-amber-100 to-yellow-50 border border-amber-200/70 text-[9px] text-amber-700 shadow-sm print-visible">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span class="font-semibold leading-tight break-words">Catatan: {{ \Illuminate\Support\Str::limit($catatanAdmin, 60) }}</span>
                    </div>
                    @endif

                    <div class="text-[11px] font-mono text-slate-600 space-y-0.5 border-t border-slate-100 pt-1">
                        @if($hasMasuk)
                        <div class="flex items-center justify-between text-[10px] text-emerald-700">
                            <span><i class="fa-solid fa-arrow-right-to-bracket mr-0.5"></i> M:</span>
                            <span class="font-bold">{{ substr($logMasuk->jam, 0, 5) }}</span>
                        </div>
                        @endif
                        @if($hasPulang)
                        <div class="flex items-center justify-between text-[10px] text-blue-700">
                            <span><i class="fa-solid fa-arrow-right-from-bracket mr-0.5"></i> P:</span>
                            <span class="font-bold">{{ substr($logPulang->jam, 0, 5) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>
</main>

<!-- MODAL DETAIL PRESENSI PER TANGGAL (SWAFOTO + GPS) -->
<div id="modalDetailTanggal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl space-y-5 p-6 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div>
                    <h3 id="modalJudulTanggal" class="text-base font-extrabold text-slate-900">Detail Absensi Harian</h3>
                    <p class="text-xs text-slate-500">Karyawan: <strong class="text-slate-800">{{ $karyawan->nama }}</strong> ({{ $karyawan->nip }})</p>
                </div>
            </div>
            <button type="button" onclick="tutupModalDetail()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Status Banner Hari Itu (Libur / Cuti / Masuk) -->
        <div id="modalBannerStatus" class="p-3 rounded-2xl border text-xs font-medium flex items-center gap-2"></div>
        <div id="modalCatatanAdmin" class="hidden p-3.5 rounded-2xl border text-xs font-medium flex items-start gap-2.5 bg-gradient-to-r from-amber-50 to-yellow-50 border-amber-200 text-amber-700 shadow-sm">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
            <div>
                <p class="font-bold">Catatan Admin</p>
                <p id="modalCatatanAdminText" class="leading-relaxed break-words"></p>
            </div>
        </div>

        <!-- 2 Kolom Absensi: Masuk & Pulang -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <span class="text-xs font-bold text-emerald-700 flex items-center gap-1.5 uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Absensi Masuk
                    </span>
                    <span id="badgeStatusMasuk" class="text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="text-center">
                    <div class="w-full h-44 rounded-xl bg-slate-200 border border-slate-300 overflow-hidden flex items-center justify-center relative shadow-inner">
                        <img id="imgFotoMasuk" src="" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition hidden" onclick="perbesarFoto(this.src)" alt="Foto Masuk" onerror="this.classList.add('hidden'); document.getElementById('emptyFotoMasuk').classList.remove('hidden');" />
                        <div id="emptyFotoMasuk" class="text-slate-400 text-xs flex flex-col items-center gap-1">
                            <i class="fa-solid fa-camera-slash text-2xl"></i>
                            <span>Tidak Ada Foto Masuk</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between"><span class="text-slate-500">Jam Masuk:</span><span id="textJamMasuk" class="font-mono font-bold text-slate-900 text-sm">-</span></div>
                    <div class="flex items-center justify-between"><span class="text-slate-500">Lokasi Titik:</span><span id="textLokasiMasuk" class="font-semibold text-slate-800 text-right truncate max-w-[160px]">-</span></div>
                    <div class="flex items-center justify-between"><span class="text-slate-500">Jarak GPS:</span><span id="textJarakMasuk" class="font-mono font-bold text-emerald-600">-</span></div>
                    <div class="flex items-center justify-between"><span class="text-slate-500">Catatan:</span><span id="textCatatanMasuk" class="text-slate-600 text-right truncate max-w-[160px]">-</span></div>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <span class="text-xs font-bold text-blue-700 flex items-center gap-1.5 uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Absensi Pulang
                    </span>
                    <span id="badgeStatusPulang" class="text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="text-center">
                    <div class="w-full h-44 rounded-xl bg-slate-200 border border-slate-300 overflow-hidden flex items-center justify-center relative shadow-inner">
                        <img id="imgFotoPulang" src="" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition hidden" onclick="perbesarFoto(this.src)" alt="Foto Pulang" onerror="this.classList.add('hidden'); document.getElementById('emptyFotoPulang').classList.remove('hidden');" />
                        <div id="emptyFotoPulang" class="text-slate-400 text-xs flex flex-col items-center gap-1">
                            <i class="fa-solid fa-camera-slash text-2xl"></i>
                            <span>Tidak Ada Foto Pulang</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between"><span class="text-slate-500">Jam Pulang:</span><span id="textJamPulang" class="font-mono font-bold text-slate-900 text-sm">-</span></div>
                    <div class="flex items-center justify-between"><span class="text-slate-500">Lokasi Titik:</span><span id="textLokasiPulang" class="font-semibold text-slate-800 text-right truncate max-w-[160px]">-</span></div>
                    <div class="flex items-center justify-between"><span class="text-slate-500">Jarak GPS:</span><span id="textJarakPulang" class="font-mono font-bold text-blue-600">-</span></div>
                    <div class="flex items-center justify-between"><span class="text-slate-500">Catatan:</span><span id="textCatatanPulang" class="text-slate-600 text-right truncate max-w-[160px]">-</span></div>
                </div>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
            <button type="button" onclick="tutupModalDetail()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition cursor-pointer shadow-sm">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function resolveSelfieUrl(foto) {
    if (!foto) return '';
    if (foto.startsWith('http://') || foto.startsWith('https://') || foto.startsWith('data:')) return foto;
    // Baris seeder memakai nama file dummy (teks, bukan file asli) → langsung pakai panel kosong
    if (!foto.match(/\.(jpg|jpeg|png|webp)$/i)) return '';
    return '/foto-selfie/' + foto;
}

function bukaModalDetailTanggal(data) {
    $('#modalJudulTanggal').text(data.tanggal_formatted + ' (' + data.hari + ')');

    let bannerHtml = '';
    if (data.masuk && data.pulang) {
        bannerHtml = '<i class="fa-solid fa-circle-check text-emerald-600 text-base"></i> <span class="text-emerald-800">Karyawan hadir lengkap pada hari ini (Absen Masuk & Pulang tercatat).</span>';
        $('#modalBannerStatus').attr('class', 'p-3 rounded-2xl border text-xs font-medium flex items-center gap-2 bg-emerald-50 border-emerald-200 text-emerald-800');
    } else if (data.masuk) {
        bannerHtml = '<i class="fa-solid fa-clock text-blue-600 text-base"></i> <span class="text-blue-800">Karyawan telah melakukan presensi masuk, namun belum tercatat presensi pulang.</span>';
        $('#modalBannerStatus').attr('class', 'p-3 rounded-2xl border text-xs font-medium flex items-center gap-2 bg-blue-50 border-blue-200 text-blue-800');
    } else if (data.cuti_name) {
        bannerHtml = '<i class="fa-solid fa-calendar-check text-purple-600 text-base"></i> <span class="text-purple-800">Karyawan sedang mengambil <strong>Cuti: ' + data.cuti_name + '</strong>.</span>';
        $('#modalBannerStatus').attr('class', 'p-3 rounded-2xl border text-xs font-medium flex items-center gap-2 bg-purple-50 border-purple-200 text-purple-800');
    } else if (data.libur_name) {
        bannerHtml = '<i class="fa-solid fa-umbrella-beach text-amber-600 text-base"></i> <span class="text-amber-800">Hari Libur Terjadwal: <strong>' + data.libur_name + '</strong>.</span>';
        $('#modalBannerStatus').attr('class', 'p-3 rounded-2xl border text-xs font-medium flex items-center gap-2 bg-amber-50 border-amber-200 text-amber-800');
    } else if (data.is_weekend) {
        bannerHtml = '<i class="fa-solid fa-bed text-slate-500 text-base"></i> <span class="text-slate-600">Hari Libur Akhir Pekan (Minggu).</span>';
        $('#modalBannerStatus').attr('class', 'p-3 rounded-2xl border text-xs font-medium flex items-center gap-2 bg-slate-100 border-slate-200 text-slate-700');
    } else {
        bannerHtml = '<i class="fa-solid fa-circle-xmark text-rose-600 text-base"></i> <span class="text-rose-800">Tidak ada catatan presensi yang terekam pada tanggal ini.</span>';
        $('#modalBannerStatus').attr('class', 'p-3 rounded-2xl border text-xs font-medium flex items-center gap-2 bg-rose-50 border-rose-200 text-rose-800');
    }
    $('#modalBannerStatus').html(bannerHtml);

    // Tampilkan catatan admin (jika ada) pada tanggal ini
    if (data.catatan_admin) {
        $('#modalCatatanAdminText').text(data.catatan_admin);
        $('#modalCatatanAdmin').removeClass('hidden');
    } else {
        $('#modalCatatanAdminText').text('');
        $('#modalCatatanAdmin').addClass('hidden');
    }

    if (data.masuk) {
        $('#textJamMasuk').text(data.masuk.jam.substring(0, 5) + ' WITA');
        $('#textLokasiMasuk').text(data.masuk.nama_lokasi || 'Kantor');
        $('#textJarakMasuk').text(data.masuk.jarak_meter + ' m (Radius Valid)');
        $('#textCatatanMasuk').text(data.masuk.catatan || '-');
        if (data.masuk.status === 'tepat_waktu') {
            $('#badgeStatusMasuk').attr('class', 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200').text('TEPAT WAKTU');
        } else {
            $('#badgeStatusMasuk').attr('class', 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200').text('TERLAMBAT');
        }
        if (data.masuk.foto_selfie) {
            $('#imgFotoMasuk').attr('src', resolveSelfieUrl(data.masuk.foto_selfie)).removeClass('hidden');
            $('#emptyFotoMasuk').addClass('hidden');
        } else {
            $('#imgFotoMasuk').addClass('hidden');
            $('#emptyFotoMasuk').removeClass('hidden');
        }
    } else {
        $('#textJamMasuk').text('-'); $('#textLokasiMasuk').text('-'); $('#textJarakMasuk').text('-'); $('#textCatatanMasuk').text('-');
        $('#badgeStatusMasuk').attr('class', 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500').text('TIDAK ABSEN');
        $('#imgFotoMasuk').addClass('hidden'); $('#emptyFotoMasuk').removeClass('hidden');
    }

    if (data.pulang) {
        $('#textJamPulang').text(data.pulang.jam.substring(0, 5) + ' WITA');
        $('#textLokasiPulang').text(data.pulang.nama_lokasi || 'Kantor');
        $('#textJarakPulang').text(data.pulang.jarak_meter + ' m (Radius Valid)');
        $('#textCatatanPulang').text(data.pulang.catatan || '-');
        $('#badgeStatusPulang').attr('class', 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200').text('SUDAH PULANG');
        if (data.pulang.foto_selfie) {
            $('#imgFotoPulang').attr('src', resolveSelfieUrl(data.pulang.foto_selfie)).removeClass('hidden');
            $('#emptyFotoPulang').addClass('hidden');
        } else {
            $('#imgFotoPulang').addClass('hidden'); $('#emptyFotoPulang').removeClass('hidden');
        }
    } else {
        $('#textJamPulang').text('-'); $('#textLokasiPulang').text('-'); $('#textJarakPulang').text('-'); $('#textCatatanPulang').text('-');
        $('#badgeStatusPulang').attr('class', 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500').text('BELUM PULANG');
        $('#imgFotoPulang').addClass('hidden'); $('#emptyFotoPulang').removeClass('hidden');
    }

    $('#modalDetailTanggal').removeClass('hidden');
}

function tutupModalDetail() {
    $('#modalDetailTanggal').addClass('hidden');
}

function perbesarFoto(url) {
    Swal.fire({ imageUrl: url, imageAlt: 'Swafoto Absensi', confirmButtonText: 'Tutup', confirmButtonColor: '#2563eb', customClass: { popup: 'rounded-3xl' } });
}
</script>
@endpush
