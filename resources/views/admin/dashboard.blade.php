@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
@include('layouts.admin-header')

<style>
.custom-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
.custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
.custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.sticky-col-1 { position: sticky; left: 0; z-index: 10; background: #ffffff; }
.sticky-col-2 { position: sticky; left: 210px; z-index: 10; background: #ffffff; }
</style>

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-5 space-y-5">
    @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Statistik -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Karyawan</p>
                    <p class="text-2xl font-black font-mono text-slate-900 mt-1">{{ $stats['total_karyawan'] }}</p>
                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">Terdaftar aktif</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-users"></i></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hadir Hari Ini</p>
                    <p class="text-2xl font-black font-mono text-emerald-600 mt-1">{{ $stats['total_hadir_hari_ini'] }} <span class="text-xs text-slate-400">({{ $stats['attendance_rate'] }}%)</span></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width: {{ $stats['attendance_rate'] }}%"></div></div>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-user-check"></i></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terlambat Hari Ini</p>
                    <p class="text-2xl font-black font-mono text-amber-600 mt-1">{{ $stats['total_terlambat'] }}</p>
                    <p class="text-[10px] {{ $stats['total_terlambat'] > 0 ? 'text-amber-500' : 'text-slate-400' }} font-medium mt-0.5">{{ $stats['total_terlambat'] > 0 ? 'Perlu Atensi' : 'Sangat Disiplin' }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-user-clock"></i></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lokasi Aktif</p>
                    <p class="text-2xl font-black font-mono text-indigo-600 mt-1">{{ $stats['total_lokasi'] }}</p>
                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">Titik geofencing GPS</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-location-dot"></i></div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-sm flex gap-1 overflow-x-auto text-xs font-semibold" id="admin-tabs">
        <button data-tab="tab-logs" class="px-4 py-2 rounded-xl bg-slate-900 text-white whitespace-nowrap cursor-pointer">Log Absensi</button>
        <button data-tab="tab-lokasi" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 whitespace-nowrap cursor-pointer">Titik Kantor</button>
        <button data-tab="tab-karyawan" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 whitespace-nowrap cursor-pointer">Karyawan</button>
        <button data-tab="tab-libur" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 whitespace-nowrap cursor-pointer">Jadwal Libur</button>
        <button data-tab="tab-role" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 whitespace-nowrap cursor-pointer">Role RBAC</button>
        <button data-tab="tab-cuti" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 whitespace-nowrap cursor-pointer">Cuti & Dispensasi</button>
        <button data-tab="tab-laporan" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 whitespace-nowrap cursor-pointer">Rekap & Ekspor</button>
        <button data-tab="tab-shift" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 whitespace-nowrap cursor-pointer">Manajemen Shift</button>
    </div>

    <!-- TAB: Log Absensi (filter by tanggal) -->
    <div id="tab-logs" class="tab-content bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-clipboard-list text-blue-600"></i> Log Absensi</h2>
            <span class="px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-[11px] font-bold">
                <i class="fa-solid fa-calendar-range"></i> {{ \Illuminate\Support\Carbon::parse($logDari)->isoFormat('D MMM Y') }} — {{ \Illuminate\Support\Carbon::parse($logSampai)->isoFormat('D MMM Y') }}
            </span>
        </div>
        <form method="GET" class="flex flex-wrap items-end gap-3 p-4 rounded-2xl bg-gradient-to-r from-slate-50 to-blue-50/50 border border-slate-200">
            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Dari <i class="fa-solid fa-play text-[7px] text-blue-400"></i></label>
                <input type="date" name="dari" value="{{ $logDari }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs shadow-sm focus:ring-2 focus:ring-blue-200 outline-none">
            </div>
            <i class="fa-solid fa-arrow-right text-slate-300 self-center mb-2 hidden sm:block"></i>
            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ $logSampai }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs shadow-sm focus:ring-2 focus:ring-blue-200 outline-none">
            </div>
            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Departemen</label>
                <select name="dept" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white shadow-sm focus:ring-2 focus:ring-blue-200 outline-none">
                    <option value="all">Semua Departemen</option>
                    @foreach($departemenList as $d)
                    <option value="{{ $d }}" {{ $logDept === $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-600/20 cursor-pointer transition active:scale-95"><i class="fa-solid fa-filter"></i> Terapkan</button>
            <a href="{{ route('admin.dashboard') }}#tab-logs" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 cursor-pointer transition">Reset <i class="fa-solid fa-rotate-left"></i></a>
        </form>

        @if($presensiHariIni->isEmpty())
        <div class="p-10 text-center rounded-2xl border-2 border-dashed border-slate-200">
            <i class="fa-regular fa-folder-open text-4xl text-slate-200 block mb-3"></i>
            <p class="text-xs font-bold text-slate-400">Tidak ada data absensi pada periode ini</p>
            <p class="text-[11px] text-slate-300 mt-1">Coba ubah rentang tanggal atau departemen</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($presensiHariIni as $row)
            <div class="p-4 rounded-2xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($row->nama) }}&background=e0e7ff&color=4338ca&size=64"
                        class="w-10 h-10 rounded-xl object-cover shrink-0" alt="">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ $row->nama }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ $row->nip }} • {{ $row->departemen }}</p>
                    </div>
                    <a href="{{ route('admin.detail-absensi', ['karyawan_id' => $row->karyawan_id, 'bulan' => \Illuminate\Support\Carbon::parse($logSampai)->month, 'tahun' => \Illuminate\Support\Carbon::parse($logSampai)->year]) }}"
                        class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 hover:bg-blue-600 hover:text-white transition cursor-pointer" title="Kalender absensi">
                        <i class="fa-solid fa-calendar-days text-xs"></i>
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-3">
                    <div class="p-2 rounded-xl bg-emerald-50 text-center">
                        <p class="text-[9px] font-bold text-emerald-500 uppercase">Hadir</p>
                        <p class="text-sm font-black text-emerald-700 font-mono">{{ $row->hari_hadir }} <span class="text-[9px] font-semibold">hr</span></p>
                    </div>
                    <div class="p-2 rounded-xl {{ $row->total_terlambat > 0 ? 'bg-amber-50' : 'bg-slate-50' }} text-center">
                        <p class="text-[9px] font-bold text-amber-500 uppercase">Telat</p>
                        <p class="text-sm font-black {{ $row->total_terlambat > 0 ? 'text-amber-700' : 'text-slate-300' }} font-mono">{{ $row->total_terlambat }} <span class="text-[9px] font-semibold">x</span></p>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 text-center">
                        <p class="text-[9px] font-bold text-slate-400 uppercase">Masuk</p>
                        <p class="text-sm font-bold {{ $row->masuk_id ? 'text-slate-700' : 'text-slate-300' }} font-mono">{{ $row->masuk_id ? substr($row->jam_masuk, 0, 5) : '—' }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2 text-[10px]">
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Pulang: <strong class="text-slate-600 font-mono">{{ $row->pulang_id ? substr($row->jam_pulang, 0, 5) : '—' }}</strong></span>
                    </div>
                    @if($row->cuti_id)
                    <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-bold">{{ strtoupper($row->tipe_cuti) }}</span>
                    @elseif($row->total_terlambat > 3)
                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-600 font-bold"><i class="fa-solid fa-bell"></i> Perlu Atensi</span>
                    @elseif($row->total_terlambat == 0)
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-600 font-bold"><i class="fa-solid fa-medal"></i> Disiplin</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- TAB: Titik Kantor -->
    <div id="tab-lokasi" class="tab-content hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-location-dot text-indigo-600"></i> Master Titik Lokasi Kantor</h2>
            <button onclick="openModalLokasi()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-plus"></i> Tambah Lokasi</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="p-2.5">Kode</th><th class="p-2.5">Nama</th><th class="p-2.5">Kota</th>
                        <th class="p-2.5">Koordinat</th><th class="p-2.5">Radius</th><th class="p-2.5">Status</th><th class="p-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($lokasiList as $lok)
                    <tr>
                        <td class="p-2.5 font-mono font-bold">{{ $lok->kode_lokasi }}</td>
                        <td class="p-2.5">{{ $lok->nama_lokasi }}</td>
                        <td class="p-2.5">{{ $lok->kota }}</td>
                        <td class="p-2.5 font-mono text-[10px]">{{ $lok->latitude }}, {{ $lok->longitude }}</td>
                        <td class="p-2.5">{{ $lok->radius }} m</td>
                        <td class="p-2.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $lok->is_active ? 'bg-emerald-500/20 text-emerald-700' : 'bg-slate-500/20 text-slate-500' }}">{{ $lok->is_active ? 'AKTIF' : 'NONAKTIF' }}</span>
                        </td>
                        <td class="p-2.5 flex gap-1.5">
                            <button onclick='editLokasi(@json($lok->toArray()))' class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg font-bold cursor-pointer"><i class="fa-solid fa-pen"></i></button>
                            <button onclick="hapusLokasi({{ $lok->id }})" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg font-bold cursor-pointer"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: Karyawan -->
    <div id="tab-karyawan" class="tab-content hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-users text-blue-600"></i> Master Data Karyawan</h2>
            <button onclick="openModalKaryawan()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-user-plus"></i> Tambah Karyawan</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="p-2.5">NIP</th><th class="p-2.5">Nama</th><th class="p-2.5">Departemen</th>
                        <th class="p-2.5">Jabatan</th><th class="p-2.5">Role</th><th class="p-2.5">Kuota Cuti</th><th class="p-2.5">Status</th><th class="p-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($karyawanList as $k)
                    <tr>
                        <td class="p-2.5 font-mono">{{ $k->nip }}</td>
                        <td class="p-2.5 font-bold">{{ $k->nama }}</td>
                        <td class="p-2.5">{{ $k->departemen }}</td>
                        <td class="p-2.5">{{ $k->jabatan }}</td>
                        <td class="p-2.5"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-{{ $k->role?->warna_badge ?? 'slate' }}-50 text-{{ $k->role?->warna_badge ?? 'slate' }}-700">{{ $k->role?->nama_role ?? '-' }}</span></td>
                        <td class="p-2.5">{{ $k->kuota_cuti_tahunan }} hari</td>
                        <td class="p-2.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $k->is_active ? 'bg-emerald-500/20 text-emerald-700' : 'bg-rose-500/20 text-rose-700' }}">{{ $k->is_active ? 'AKTIF' : 'NONAKTIF' }}</span>
                        </td>
                        <td class="p-2.5">
                            <div class="flex gap-1.5">
                                <button onclick='editKaryawan(@json($k->toArray()))' class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg font-bold cursor-pointer"><i class="fa-solid fa-pen"></i></button>
                                <button onclick="toggleKaryawan({{ $k->id }}, {{ $k->is_active ? 0 : 1 }})" class="px-2 py-1 bg-amber-50 text-amber-600 rounded-lg font-bold cursor-pointer" title="{{ $k->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"><i class="fa-solid fa-power-off"></i></button>
                                <button onclick="resetPasswordKaryawan({{ $k->id }})" class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg font-bold cursor-pointer" title="Reset Password"><i class="fa-solid fa-key"></i></button>
                                <button onclick="hapusKaryawan({{ $k->id }})" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg font-bold cursor-pointer"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: Jadwal Libur -->
    <div id="tab-libur" class="tab-content hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-calendar-days text-amber-600"></i> Master Jadwal Libur</h2>
            <a href="{{ route('admin.jadwal-libur.index') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-arrow-up-right-from-square"></i> Kelola Jadwal Libur</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($jadwalLiburList as $libur)
            <div class="p-3.5 rounded-2xl border border-slate-200 bg-amber-50/50">
                <p class="text-xs font-bold text-slate-900">{{ $libur->nama_libur }}</p>
                <p class="text-[10px] text-slate-500 mt-1">{{ $libur->tanggal_mulai->translatedFormat('d M Y') }} — {{ $libur->tanggal_selesai->translatedFormat('d M Y') }}</p>
                <span class="inline-block mt-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">{{ strtoupper($libur->tipe_target) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- TAB: Role RBAC -->
    <div id="tab-role" class="tab-content hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-shield-halved text-purple-600"></i> Manajemen Role &amp; Hak Akses</h2>
            <button type="button" onclick="openModalRole()" style="background-color:#9333ea;color:#fff;" class="px-4 py-2 hover:bg-purple-700 rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-plus"></i> Tambah Role</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($rolesList as $role)
            <div class="p-4 rounded-2xl border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-900">{{ $role->nama_role }}</p>
                        <p class="text-[10px] font-mono text-slate-400">{{ $role->kode_role }} • {{ $role->total_users }} user</p>
                    </div>
                    <div class="flex gap-1.5">
                        @if($role->kode_role !== 'ROLE_SUPER_ADMIN')
                        <button onclick='openModalRole(@json($role->toArray()))' class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg font-bold cursor-pointer" title="Edit Role"><i class="fa-solid fa-pen text-xs"></i></button>
                        <button onclick="hapusRole({{ $role->id }})" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg cursor-pointer" title="Hapus Role"><i class="fa-solid fa-trash text-xs"></i></button>
                        @else
                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-purple-50 text-purple-600" title="Super Administrator tidak dapat diubah"><i class="fa-solid fa-lock text-xs"></i></span>
                        @endif
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 leading-relaxed">{{ $role->deskripsi }}</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($allPermissions as $pkey => $plabel)
                        @if(in_array($pkey, $role->izin_akses ?? []))
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700">{{ $plabel }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Form Role -->
    <div id="modal-role" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl max-h-[92vh] overflow-y-auto">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <div>
                    <h3 class="text-sm font-bold text-slate-900" id="modal-role-title">Tambah Role</h3>
                    <p class="text-[11px] text-slate-500">Atur hak akses menu & fitur</p>
                </div>
                <button type="button" onclick="tutupModalRole()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="form-role" class="p-5 space-y-3">
                <div id="wrap-role-kode">
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Kode Role *</label>
                    <input type="text" name="kode_role" id="role-kode" required placeholder="cth: ROLE_KEPALA_SEKOLAH"
                        class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono uppercase">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Nama Role *</label>
                    <input type="text" name="nama_role" id="role-nama" required
                        class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Deskripsi</label>
                    <textarea name="deskripsi" id="role-deskripsi" rows="2"
                        class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Hak Akses</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 mt-1 p-3 rounded-xl border border-slate-200 max-h-56 overflow-y-auto">
                        @foreach($allPermissions as $pkey => $plabel)
                        <label class="flex items-center gap-2 text-[11px] text-slate-600 cursor-pointer">
                            <input type="checkbox" name="perm[]" value="{{ $pkey }}" class="perm-cb rounded border-slate-300">
                            {{ $plabel }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Role
                </button>
            </form>
        </div>
    </div>

    <!-- TAB: Cuti & Dispensasi -->
    <div id="tab-cuti" class="tab-content hidden space-y-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-envelope-open-text text-amber-600"></i> Pending: Pengajuan Cuti ({{ $pendingCutiList->count() }})</h2>
            @forelse($pendingCutiList as $cuti)
            <div class="p-3.5 rounded-xl border border-amber-200 bg-amber-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <p class="text-xs font-bold text-slate-900">{{ $cuti->karyawan->nama }} — {{ $cuti->label_tipe }}</p>
                    <p class="text-[10px] text-slate-500">{{ $cuti->tanggal_mulai->translatedFormat('d M Y') }} s/d {{ $cuti->tanggal_selesai->translatedFormat('d M Y') }} ({{ $cuti->total_hari }} hari) • Approver: {{ $cuti->target_approver_name }}</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="approveCuti({{ $cuti->id }}, 'approve')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold cursor-pointer"><i class="fa-solid fa-check"></i> Setujui</button>
                    <button onclick="approveCuti({{ $cuti->id }}, 'reject')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-[11px] font-bold cursor-pointer"><i class="fa-solid fa-xmark"></i> Tolak</button>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-400 italic">Tidak ada pengajuan cuti pending.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-hand text-blue-600"></i> Pending: Dispensasi ({{ $pendingDispensasiList->count() }})</h2>
            @forelse($pendingDispensasiList as $pl)
            <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <p class="text-xs font-bold text-slate-900">{{ $pl->karyawan->nama }} — {{ $pl->label_tipe }}</p>
                    <p class="text-[10px] text-slate-500">{{ $pl->tanggal->translatedFormat('d M Y') }} • Estimasi {{ substr($pl->jam_estimasi, 0, 5) }} • {{ $pl->alasan }}</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="approveDispensasi({{ $pl->id }}, 'approve')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-[11px] font-bold cursor-pointer"><i class="fa-solid fa-check"></i></button>
                    <button onclick="approveDispensasi({{ $pl->id }}, 'reject')" class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-[11px] font-bold cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-400 italic">Tidak ada dispensasi pending.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-chart-simple text-indigo-600"></i> Monitoring Kuota Cuti {{ now()->year }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 uppercase border-b border-slate-100">
                        <tr><th class="p-2.5">Karyawan</th><th class="p-2.5">Total</th><th class="p-2.5">Terpakai</th><th class="p-2.5">Pending</th><th class="p-2.5">Sisa</th><th class="p-2.5">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($kuotaRekap as $item)
                        <tr>
                            <td class="p-2.5 font-bold">{{ $item['karyawan']->nama }}</td>
                            <td class="p-2.5">{{ $item['kuota']['total_quota'] }}</td>
                            <td class="p-2.5 text-amber-600">{{ $item['kuota']['used_quota'] }}</td>
                            <td class="p-2.5 text-blue-600">{{ $item['kuota']['pending_quota'] }}</td>
                            <td class="p-2.5 font-bold text-emerald-600">{{ $item['kuota']['remaining_quota'] }}</td>
                            <td class="p-2.5">
                                <button onclick="editKuota({{ $item['karyawan']->id }}, '{{ addslashes($item['karyawan']->nama) }}', {{ $item['kuota']['total_quota'] }})" class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg cursor-pointer"><i class="fa-solid fa-pen text-xs"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: Rekap & Ekspor -->
    <div id="tab-laporan" class="tab-content hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-file-export text-emerald-600"></i> Rekapitulasi & Ekspor Laporan</h2>
        <form method="GET" action="{{ route('admin.rekap') }}" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label class="text-[11px] font-bold text-slate-600">Bulan</label>
                <select name="bulan" class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i === now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Tahun</label>
                <input type="number" name="tahun" value="{{ now()->year }}" class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Departemen</label>
                <select name="dept" class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <option value="all">Semua</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold cursor-pointer">Lihat Rekap</button>
            </div>
        </form>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.export.excel', ['bulan' => now()->month, 'tahun' => now()->year]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-file-excel"></i> Unduh Excel</a>
            <a href="{{ route('admin.export.pdf', ['bulan' => now()->month, 'tahun' => now()->year]) }}" target="_blank" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-file-pdf"></i> Unduh PDF (Print)</a>
        </div>
    </div>

    <!-- TAB: Shift -->
    <div id="tab-shift" class="tab-content hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-business-time text-purple-600"></i> Master Shift & Roster</h2>
            <button type="button" onclick="openModalShift()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-plus"></i> Tambah Shift</button>
        </div>

        <!-- Master Shift -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-100">
                    <tr><th class="p-2.5">Kode</th><th class="p-2.5">Nama</th><th class="p-2.5">Jam</th><th class="p-2.5">Tipe</th><th class="p-2.5">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php $shiftsList = \App\Models\MasterShift::orderBy('is_off')->orderBy('jam_masuk')->get(); @endphp
                    @foreach($shiftsList as $shift)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-2.5 font-mono font-bold">{{ $shift->kode_shift }}</td>
                        <td class="p-2.5">{{ $shift->nama_shift }}</td>
                        <td class="p-2.5 font-mono">{{ substr($shift->jam_masuk, 0, 5) }} - {{ substr($shift->jam_pulang, 0, 5) }}</td>
                        <td class="p-2.5"><span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $shift->is_off ? 'bg-slate-100 text-slate-500' : ($shift->is_night_shift ? 'bg-purple-50 text-purple-700' : 'bg-emerald-50 text-emerald-700') }}">{{ $shift->is_off ? 'OFF' : ($shift->is_night_shift ? 'NIGHT' : 'REGULER') }}</span></td>
                        <td class="p-2.5"><div class="flex gap-1.5">
                            <button onclick='openModalShift(@json($shift->toArray()))' class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg font-bold cursor-pointer"><i class="fa-solid fa-pen"></i></button>
                            <button onclick="hapusShift({{ $shift->id }})" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg font-bold cursor-pointer"><i class="fa-solid fa-trash"></i></button>
                        </div></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Roster Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-3 border-t border-slate-100">
            <div class="space-y-2 flex flex-col justify-between h-full">
                <h3 class="text-xs font-bold text-slate-900"><i class="fa-solid fa-calendar-check text-blue-600"></i> Assign Roster Satu Tanggal</h3>
                <p class="text-[11px] text-slate-500">Set shift untuk satu karyawan pada satu tanggal tertentu.</p>
                <button type="button" onclick="openModalAssign()" class="mt-auto w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold cursor-pointer active:scale-95 transition"><i class="fa-solid fa-calendar-plus"></i> Buka Form Assign Roster</button>
            </div>
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-900"><i class="fa-solid fa-wand-magic-sparkles text-amber-600"></i> Auto-Generate Roster Bulanan</h3>
                <p class="text-[11px] text-slate-500">Generate roster sebulan penuh sesuai pola. Jam karyawan Tetap & Kontrak bisa di-set saat generate (default dari konfigurasi).</p>
                <button type="button" onclick="openModalGenerate()" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold cursor-pointer active:scale-95 transition"><i class="fa-solid fa-wand-magic-sparkles"></i> Buka Form Generate Roster</button>
            </div>
        </div>

                <!-- Matriks Kalender Roster Kerja Bulanan (full width, di luar grid 2 kolom) -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-calendar-week text-indigo-600"></i> Matriks Kalender Roster Kerja Bulanan
                            </h3>
                            <p class="text-[11px] text-slate-500">Pilih shift harian per karyawan dengan klik langsung pada sel tanggal</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.dashboard') }}?roster_bulan={{ $rosterPrev['bulan'] }}&roster_tahun={{ $rosterPrev['tahun'] }}#tab-shift" class="p-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl text-slate-700 transition shadow-sm" title="Bulan Sebelumnya">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                            <form method="GET" class="flex items-center gap-2">
                                <input type="hidden" name="dari" value="{{ $logDari }}"><input type="hidden" name="sampai" value="{{ $logSampai }}">
                                <select name="roster_bulan" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-1.5 text-xs font-bold focus:outline-none focus:border-indigo-500">
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m === $rosterBulan ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                                    @endfor
                                </select>
                                <select name="roster_tahun" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-1.5 text-xs font-bold focus:outline-none focus:border-indigo-500">
                                    @for($y = 2024; $y <= 2028; $y++)
                                    <option value="{{ $y }}" {{ $y === $rosterTahun ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </form>
                            <a href="{{ route('admin.dashboard') }}?roster_bulan={{ $rosterNext['bulan'] }}&roster_tahun={{ $rosterNext['tahun'] }}#tab-shift" class="p-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl text-slate-700 transition shadow-sm" title="Bulan Berikutnya">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Filter & Search Bar Roster Matrix -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        <div class="relative flex-1">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" id="searchRosterKaryawan" placeholder="Cari nama atau NIP karyawan..." class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" />
                            <button type="button" id="btnResetSearchRoster" onclick="$('#searchRosterKaryawan').val(''); filterRosterKaryawan();" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden text-xs cursor-pointer">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5">
                                <label for="filterDeptRoster" class="text-xs font-bold text-slate-600 shrink-0 flex items-center gap-1"><i class="fa-solid fa-building text-blue-600"></i> Dept:</label>
                                <select id="filterDeptRoster" class="bg-white border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer">
                                    <option value="all">Semua Departemen</option>
                                    @foreach($departemenList as $dept)
                                    <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" onclick="resetFilterRoster()" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 rounded-xl transition text-xs cursor-pointer" title="Reset Semua Filter"><i class="fa-solid fa-rotate-left"></i></button>
                            <span id="counterRosterText" class="text-[11px] font-semibold px-2.5 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl whitespace-nowrap">Menampilkan {{ $karyawanList->count() }} Karyawan</span>
                        </div>
                    </div>

                    <!-- Matrix Table -->
                    <div class="overflow-x-auto custom-scroll rounded-2xl border border-slate-200">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-100/90 text-slate-700 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-3 sticky-col-1 w-[210px] min-w-[210px] max-w-[210px] border-r border-slate-200 bg-slate-100">Karyawan</th>
                                    <th class="py-3 px-2 sticky-col-2 w-[120px] min-w-[120px] max-w-[120px] border-r border-slate-200 bg-slate-100">Departemen</th>
                                    @for($d = 1; $d <= $daysInRosterMonth; $d++)
                                        @php
                                            $dateStr = sprintf('%04d-%02d-%02d', $rosterTahun, $rosterBulan, $d);
                                            $dayName = date('D', strtotime($dateStr));
                                            $isWeekend = ($dayName === 'Sun');
                                        @endphp
                                        <th class="py-3 px-1.5 text-center min-w-[46px] border-r border-slate-200 {{ $isWeekend ? 'bg-rose-50 text-rose-700 font-bold' : '' }}">
                                            <div class="text-[11px] font-bold">{{ $d }}</div>
                                            <div class="text-[8px] font-normal {{ $isWeekend ? 'text-rose-500' : 'text-slate-500' }}">{{ $dayName }}</div>
                                        </th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($karyawanList as $emp)
                                <tr class="row-roster hover:bg-slate-50/80 transition" data-nama="{{ strtolower($emp->nama) }}" data-nip="{{ strtolower($emp->nip) }}" data-dept="{{ strtolower($emp->departemen) }}">
                                    <td class="py-2.5 px-3 sticky-col-1 w-[210px] min-w-[210px] max-w-[210px] border-r border-slate-200 bg-white">
                                        <div class="font-bold text-slate-900 truncate">{{ $emp->nama }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">{{ $emp->nip }}</div>
                                    </td>
                                    <td class="py-2.5 px-2 sticky-col-2 w-[120px] min-w-[120px] max-w-[120px] border-r border-slate-200 bg-white text-[11px] text-slate-600 font-medium"><span class="block truncate">{{ $emp->departemen }}</span></td>
                                    @for($d = 1; $d <= $daysInRosterMonth; $d++)
                                        @php
                                            $dateStr = sprintf('%04d-%02d-%02d', $rosterTahun, $rosterBulan, $d);
                                            $rosterItem = $rosterMap[$emp->id.'_'.$dateStr] ?? null;
                                            $dayName = date('D', strtotime($dateStr));
                                            $isWeekend = ($dayName === 'Sun');
                                        @endphp
                                        <td class="py-2 px-1.5 text-center border-r border-slate-100 {{ $isWeekend ? 'bg-rose-50/40' : '' }}">
                                <button type="button" onclick="openModalAssign({{ $emp->id }}, '{{ $dateStr }}')"
                                                class="w-full max-w-[54px] mx-auto py-1.5 rounded-lg text-[9px] font-mono font-bold transition flex items-center justify-center cursor-pointer shadow-xs {{ $rosterItem ? ($rosterItem['is_off'] ? 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100') : 'bg-slate-50 text-slate-400 border border-dashed border-slate-200 hover:bg-slate-100' }}"
                                                title="Klik untuk ubah shift tanggal {{ $dateStr }}">
                                                {{ $rosterItem ? $rosterItem['kode_shift'] : 'PAGI' }}
                                            </button>
                                        </td>
                                    @endfor
                                </tr>
                                @endforeach
                                <tr id="rowEmptyRoster" class="hidden">
                                    <td colspan="{{ $daysInRosterMonth + 2 }}" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <i class="fa-solid fa-user-xmark text-slate-300 text-2xl"></i>
                                            <span class="text-xs font-semibold">Tidak ada karyawan yang cocok dengan pencarian / filter departemen.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend Info -->
                    <div class="flex flex-wrap items-center gap-3 pt-2 text-[11px] text-slate-600 bg-slate-50 border border-slate-200 rounded-2xl p-3">
                        <span class="font-bold text-slate-800">Keterangan:</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-indigo-50 border border-indigo-300"></span> Shift Kerja Masuk</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-200 border border-slate-300"></span> Libur / Day Off</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-rose-100 border border-rose-300"></span> Hari Libur Akhir Pekan (Minggu)</span>
                    </div>
                </div>
            </div>



    <!-- Modal Form Shift -->
    <div id="modal-shift" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900" id="modal-shift-title">Tambah Shift</h3>
                <button type="button" onclick="tutupModalShift()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="form-shift" class="p-5 space-y-3">
                <div id="wrap-kode-shift">
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Kode Shift *</label>
                    <input type="text" name="kode_shift" id="shift-kode" required placeholder="cth: S-SORE" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono uppercase">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Nama Shift *</label>
                    <input type="text" name="nama_shift" id="shift-nama" required class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="text-[11px] font-bold text-slate-500 uppercase">Jam Masuk</label><input type="time" name="jam_masuk" id="shift-masuk" value="08:00" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs"></div>
                    <div><label class="text-[11px] font-bold text-slate-500 uppercase">Jam Pulang</label><input type="time" name="jam_pulang" id="shift-pulang" value="17:00" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs"></div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 text-xs text-slate-600"><input type="checkbox" name="is_night_shift" id="shift-night" class="rounded border-slate-300"> Night Shift</label>
                    <label class="flex items-center gap-2 text-xs text-slate-600"><input type="checkbox" name="is_off" id="shift-off" class="rounded border-slate-300"> Day Off</label>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Warna Badge</label>
                    <select name="warna_badge" id="shift-warna" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        @foreach(['emerald','blue','purple','amber','rose','cyan','slate'] as $w)
                        <option value="{{ $w }}">{{ ucfirst($w) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Deskripsi</label>
                    <textarea name="deskripsi" id="shift-deskripsi" rows="2" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-floppy-disk"></i> Simpan Shift</button>
            </form>
        </div>
    </div>
</main>

<!-- Modal Lokasi -->
<div id="modal-lokasi" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white rounded-3xl p-5 space-y-3">
        <h3 id="modal-lokasi-title" class="text-sm font-bold text-slate-900">Tambah Titik Kantor</h3>
        <form id="form-lokasi" class="space-y-3">
            <input type="hidden" name="id" id="lokasi-id">
            <input type="text" name="kode" id="lokasi-kode" placeholder="Kode (cth: KTR-JKT)" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <input type="text" name="nama" id="lokasi-nama" placeholder="Nama lokasi" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <input type="text" name="alamat" id="lokasi-alamat" placeholder="Alamat" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <input type="text" name="kota" id="lokasi-kota" placeholder="Kota" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <div class="grid grid-cols-2 gap-2">
                <input type="number" step="any" name="lat" id="lokasi-lat" placeholder="Latitude" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <input type="number" step="any" name="lng" id="lokasi-lng" placeholder="Longitude" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
            <input type="number" name="radius" id="lokasi-radius" placeholder="Radius (meter)" value="100" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <!-- GPS Auto Detect & Interactive Leaflet Map -->
            <div class="bg-slate-100 p-3 rounded-2xl border border-slate-300 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800"><i class="fa-solid fa-map-location-dot text-amber-500 mr-1"></i> Titik Koordinat GPS</span>
                    <button type="button" id="btn-gps-saya" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold flex items-center gap-1 transition cursor-pointer active:scale-95">
                        <i class="fa-solid fa-crosshairs"></i> Deteksi Lokasi Saya
                    </button>
                </div>
                <div id="lokasi-gps-alert" class="hidden text-[11px] text-emerald-700 bg-emerald-100 p-1.5 rounded-lg border border-emerald-300"></div>
                <div class="relative" style="z-index: 1200;">
                    <input type="text" id="lokasi-search" placeholder="Cari alamat lokasi (cth: Jl. Ahmad Yani Denpasar)..." autocomplete="off"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-300">
                    <div id="lokasi-search-results" class="hidden absolute w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-y-auto" style="z-index: 1300;"></div>
                </div>
                <div class="text-[10px] text-blue-700 font-medium">Klik / geser pin pada peta untuk memilih titik lokasi:</div>
                <div id="lokasi-map" class="h-52 rounded-xl border border-slate-300 relative" style="z-index: 0;"></div>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeModalLokasi()" class="flex-1 py-2.5 bg-slate-100 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Karyawan -->
<div id="modal-karyawan" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white rounded-3xl p-5 space-y-3 max-h-[90vh] overflow-y-auto">
        <h3 id="modal-karyawan-title" class="text-sm font-bold text-slate-900">Tambah Karyawan</h3>
        <form id="form-karyawan" class="space-y-3">
            <input type="hidden" name="id" id="karyawan-id">
            <input type="text" name="nip" id="karyawan-nip" placeholder="NIP" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <input type="text" name="nama" id="karyawan-nama" placeholder="Nama lengkap" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <input type="email" name="email" id="karyawan-email" placeholder="Email" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <div class="grid grid-cols-2 gap-2">
                <input type="text" name="departemen" id="karyawan-departemen" placeholder="Departemen" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <input type="text" name="jabatan" id="karyawan-jabatan" placeholder="Jabatan" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <select name="role_id" id="karyawan-role" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    @foreach($rolesList as $role)
                        <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                    @endforeach
                </select>
                <select name="agama" id="karyawan-agama" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $agama)
                        <option value="{{ $agama }}">{{ $agama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input type="time" name="jam_masuk" id="karyawan-jam-masuk" value="08:00" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <input type="time" name="jam_pulang" id="karyawan-jam-pulang" value="17:00" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
            <input type="password" name="password" id="karyawan-password" placeholder="Password awal (default: 123456)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <div class="flex gap-2">
                <button type="button" onclick="closeModalKaryawan()" class="flex-1 py-2.5 bg-slate-100 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function ajaxPost(url, data, onSuccess) {
    // FormData friendly
    $.ajax({ url, type: 'POST', data: { ...data, _token: CSRF }, dataType: 'json',
        success: (res) => {
            if (res.status === 'success') { Swal.fire({icon:'success', title:'Berhasil', text:res.message, timer:1600, showConfirmButton:false}).then(onSuccess || (()=>location.reload())); }
            else Swal.fire({icon:'error', title:'Gagal', text:res.message});
        },
        error: (xhr) => {
            const msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
            Swal.fire({icon:'error', title:'Gagal', text: msg});
        }
    });
}

// Tabs
// Tab aktif dipersist di sessionStorage agar tidak reset saat refresh/submit
function aktifkanTab(tabId) {
    if (!$('#' + tabId).length) tabId = 'tab-logs';
    $('#admin-tabs button').removeClass('bg-slate-900 text-white').addClass('text-slate-600 hover:bg-slate-100');
    $('#admin-tabs button[data-tab="' + tabId + '"]').addClass('bg-slate-900 text-white').removeClass('text-slate-600 hover:bg-slate-100');
    $('.tab-content').addClass('hidden');
    $('#' + tabId).removeClass('hidden');
    sessionStorage.setItem('dashboard.activeTab', tabId);
}

$('#admin-tabs button').click(function () {
    aktifkanTab($(this).data('tab'));
});

aktifkanTab(sessionStorage.getItem('dashboard.activeTab') || 'tab-logs');

// ===== Lokasi =====
let lokasiMap = null, lokasiMarker = null;

function setLokasiPick(lat, lng, doFly) {
    $('#lokasi-lat').val(lat.toFixed(6));
    $('#lokasi-lng').val(lng.toFixed(6));
    if (lokasiMarker) lokasiMarker.setLatLng([lat, lng]);
    if (doFly) lokasiMap.flyTo([lat, lng], 17);
}

function initLokasiMap() {
    if (lokasiMap) { setTimeout(() => lokasiMap.invalidateSize(), 100); return; }
    lokasiMap = L.map('lokasi-map', { attributionControl: true }).setView([-8.6075, 115.2135], 12);

    // Skin ala Google Maps: OpenFreeMap style "liberty" (vector, gratis tanpa API key)
    const glLayer = L.maplibreGL({
        style: 'https://tiles.openfreemap.org/styles/liberty',
        updateWhenIdle: false,
    });
    glLayer.addTo(lokasiMap);

    // Fallback raster OSM bila style vector gagal dimuat
    const tileOSM = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    });
    glLayer.getMaplibreMap().on('error', function (e) {
        if (e?.error && !lokasiMap.hasLayer(tileOSM)) {
            lokasiMap.removeLayer(glLayer);
            tileOSM.addTo(lokasiMap);
        }
    });

    // Pin draggable — klik & geser pin untuk memilih titik (sesuai app asli)
    lokasiMarker = L.marker([-8.6075, 115.2135], { draggable: true }).addTo(lokasiMap);

    lokasiMap.on('click', function (e) {
        setLokasiPick(e.latlng.lat, e.latlng.lng, false);
    });

    lokasiMarker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        setLokasiPick(pos.lat, pos.lng, false);
    });

    // Deteksi Lokasi Saya (geolokasi browser)
    $('#btn-gps-saya').off('click').on('click', function () {
        const btn = $(this);
        if (!navigator.geolocation) {
            Swal.fire({ icon: 'warning', title: 'Geolokasi tidak didukung', text: 'Browser Anda tidak mendukung Geolocation.' });
            return;
        }
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Mendeteksi...');
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = parseFloat(pos.coords.latitude.toFixed(6));
                const lng = parseFloat(pos.coords.longitude.toFixed(6));
                setLokasiPick(lat, lng, true);
                $('#lokasi-gps-alert').removeClass('hidden').html('<i class="fa-solid fa-circle-check"></i> Lokasi berhasil dideteksi: ' + lat + ', ' + lng + ' (Akurasi: ~' + Math.round(pos.coords.accuracy) + ' m)');
                btn.prop('disabled', false).html('<i class="fa-solid fa-crosshairs"></i> Deteksi Lokasi Saya');
            },
            function (err) {
                Swal.fire({ icon: 'error', title: 'Gagal deteksi lokasi', text: err.message });
                btn.prop('disabled', false).html('<i class="fa-solid fa-crosshairs"></i> Deteksi Lokasi Saya');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });

    // Pencarian alamat + sugesti (Nominatim / OpenStreetMap — gratis tanpa API key)
    let searchTimer = null;

    $('#lokasi-search').off('input').on('input', function () {
        const q = $(this).val().trim();
        clearTimeout(searchTimer);
        if (q.length < 3) {
            $('#lokasi-search-results').addClass('hidden').empty();
            return;
        }
        searchTimer = setTimeout(function () {
            $('#lokasi-search-results').removeClass('hidden').html('<div class="p-3 text-[11px] text-slate-400"><i class="fa-solid fa-spinner fa-spin"></i> Mencari lokasi...</div>');
            $.getJSON('https://nominatim.openstreetmap.org/search', {
                q: q + ' Indonesia', format: 'json', limit: 6, 'accept-language': 'id',
            }).done(function (res) {
                if (!res.length) {
                    $('#lokasi-search-results').html('<div class="p-3 text-[11px] text-slate-400">Lokasi tidak ditemukan.</div>');
                    return;
                }
                console.log(res);
                window._lokasiSearchResults = res;
                $('#lokasi-search-results').html(res.map((item, idx) => `
                    <div class="p-2.5 hover:bg-blue-50 cursor-pointer border-b border-slate-50 text-xs" onclick="pilihHasilCari(${idx})">
                        <p class="font-bold text-slate-800 truncate"><i class="fa-solid fa-location-dot text-blue-500"></i> ${item.display_name.split(',')[0]}</p>
                        <p class="text-[10px] text-slate-400 truncate">${item.display_name}</p>
                    </div>`));
            }).fail(function () {
                $('#lokasi-search-results').html('<div class="p-3 text-[11px] text-rose-500">Layanan pencarian sedang tidak tersedia, coba lagi.</div>');
            });
        }, 450);
    });

    $(document).off('click.lokasiSearchOut').on('click.lokasiSearchOut', function (e) {
        if (!$(e.target).closest('#lokasi-search, #lokasi-search-results').length) {
            $('#lokasi-search-results').addClass('hidden');
        }
    });
}

function pilihHasilCari(idx) {
    const item = (window._lokasiSearchResults || [])[idx];
    if (!item) return;
    const lat = parseFloat(item.lat), lng = parseFloat(item.lon);
    if (lokasiMap && lokasiMarker) setLokasiPick(lat, lng, true);
    // Isi alamat dari data Nominatim bila tersedia
    if (item.display_name) {
        const parts = item.display_name.split(',');
        $('#lokasi-alamat').val(parts.slice(0, 3).join(',').trim());
        const kota = item.address && (item.address.city || item.address.town || item.address.village || item.address.county);
        if (kota) $('#lokasi-kota').val(kota);
    }
    $('#lokasi-search-results').addClass('hidden').empty();
}

function openModalLokasi() {
    $('#modal-lokasi-title').text('Tambah Titik Kantor');
    $('#form-lokasi')[0].reset();
    $('#lokasi-id').val('');
    initLokasiMap();
    setLokasiPick(-8.6075, 115.2135, false);
    $('#lokasi-gps-alert').addClass('hidden');
    $('#modal-lokasi').removeClass('hidden').addClass('flex');
    setTimeout(() => lokasiMap.invalidateSize(), 100);
}
function closeModalLokasi() { $('#modal-lokasi').addClass('hidden').removeClass('flex'); }

function editLokasi(l) {
    $('#modal-lokasi-title').text('Edit: ' + l.nama_lokasi);
    $('#lokasi-id').val(l.id); $('#lokasi-kode').val(l.kode_lokasi); $('#lokasi-nama').val(l.nama_lokasi);
    $('#lokasi-alamat').val(l.alamat); $('#lokasi-kota').val(l.kota);
    $('#lokasi-lat').val(l.latitude); $('#lokasi-lng').val(l.longitude); $('#lokasi-radius').val(l.radius);
    initLokasiMap();
    setLokasiPick(parseFloat(l.latitude), parseFloat(l.longitude), true);
    $('#modal-lokasi').removeClass('hidden').addClass('flex');
    setTimeout(() => lokasiMap.invalidateSize(), 100);
}

$('#form-lokasi').on('submit', function (e) {
    e.preventDefault();
    const id = $('#lokasi-id').val();
    const data = Object.fromEntries(new FormData(this).entries());
    data.action = id ? 'edit' : 'tambah';
    ajaxPost("{{ route('admin.ajax.crud-lokasi') }}", data);
});

function hapusLokasi(id) {
    Swal.fire({icon:'warning', title:'Hapus lokasi?', showCancelButton:true, confirmButtonText:'Hapus'}).then(r => {
        if (r.isConfirmed) ajaxPost("{{ route('admin.ajax.crud-lokasi') }}", { action: 'hapus', id });
    });
}

// ===== Karyawan =====
function openModalKaryawan() {
    $('#modal-karyawan-title').text('Tambah Karyawan');
    $('#form-karyawan')[0].reset();
    $('#karyawan-id').val('');
    $('#modal-karyawan').removeClass('hidden').addClass('flex');
}
function closeModalKaryawan() { $('#modal-karyawan').addClass('hidden').removeClass('flex'); }

function editKaryawan(k) {
    $('#modal-karyawan-title').text('Edit: ' + k.nama);
    $('#karyawan-id').val(k.id); $('#karyawan-nama').val(k.nama); $('#karyawan-email').val(k.email);
    $('#karyawan-departemen').val(k.departemen); $('#karyawan-jabatan').val(k.jabatan);
    $('#karyawan-role').val(k.role_id); $('#karyawan-agama').val(k.agama);
    $('#karyawan-nip').val(k.nip).prop('disabled', true);
    $('#karyawan-password').val('').closest('div').hide();
    $('#modal-karyawan').removeClass('hidden').addClass('flex');
}

$('#form-karyawan').on('submit', function (e) {
    e.preventDefault();
    const id = $('#karyawan-id').val();
    const data = Object.fromEntries(new FormData(this).entries());
    data.action = id ? 'edit' : 'tambah';
    if (id) delete data.password;
    ajaxPost("{{ route('admin.ajax.crud-karyawan') }}", data);
});

function hapusKaryawan(id) {
    Swal.fire({icon:'warning', title:'Hapus karyawan & riwayatnya?', showCancelButton:true, confirmButtonText:'Hapus'}).then(r => {
        if (r.isConfirmed) ajaxPost("{{ route('admin.ajax.crud-karyawan') }}", { action: 'hapus', id });
    });
}

function toggleKaryawan(id, status) {
    ajaxPost("{{ route('admin.ajax.crud-karyawan') }}", { action: 'toggle_status', id, status });
}

function resetPasswordKaryawan(id) {
    Swal.fire({
        title: 'Reset Password',
        input: 'text',
        inputLabel: 'Password baru (min. 5 karakter)',
        showCancelButton: true,
    }).then(r => {
        if (r.isConfirmed && r.value && r.value.length >= 5) {
            ajaxPost("{{ route('admin.ajax.crud-karyawan') }}", { action: 'reset_password', id, password: r.value });
        }
    });
}

// ===== Cuti & dispensasi =====
function approveCuti(id, action) {
    const doIt = (alasan = '') => ajaxPost("{{ route('admin.ajax.approve-cuti') }}", { cuti_id: id, action, alasan_penolakan: alasan });
    if (action === 'reject') {
        Swal.fire({ title: 'Alasan penolakan', input: 'textarea', showCancelButton: true }).then(r => { if (r.isConfirmed && r.value) doIt(r.value); });
    } else doIt();
}

function approveDispensasi(id, action) {
    const doIt = (alasan = '') => ajaxPost("{{ route('admin.ajax.approve-pengajuan-lain') }}", { pengajuan_id: id, action, alasan_penolakan: alasan });
    if (action === 'reject') {
        Swal.fire({ title: 'Alasan penolakan', input: 'textarea', showCancelButton: true }).then(r => { if (r.isConfirmed && r.value) doIt(r.value); });
    } else doIt();
}

function editKuota(id, nama, kuota) {
    Swal.fire({ title: 'Kuota Cuti: ' + nama, input: 'number', inputValue: kuota, showCancelButton: true })
        .then(r => { if (r.isConfirmed) ajaxPost("{{ route('admin.ajax.update-saldo-cuti') }}", { karyawan_id: id, kuota_baru: r.value }); });
}

function hapusRole(id) {
    Swal.fire({icon:'warning', title:'Hapus role ini?', showCancelButton:true}).then(r => {
        if (r.isConfirmed) ajaxPost("{{ route('admin.ajax.crud-role') }}", { action: 'hapus', id });
    });
}

// ===== Shift & Roster =====
function openModalShift(shift) {
    $('#form-shift')[0].reset();
    $('#form-shift').data('id', shift?.id || '');
    $('#modal-shift-title').text(shift ? 'Edit Shift: ' + shift.nama_shift : 'Tambah Shift');
    $('#wrap-kode-shift').toggle(!shift);
    $('#shift-nama').val(shift?.nama_shift || '');
    $('#shift-masuk').val(shift ? (shift.jam_masuk || '').slice(0,5) : '08:00');
    $('#shift-pulang').val(shift ? (shift.jam_pulang || '').slice(0,5) : '17:00');
    $('#shift-night').prop('checked', !!shift?.is_night_shift);
    $('#shift-off').prop('checked', !!shift?.is_off);
    $('#shift-warna').val(shift?.warna_badge || 'blue');
    $('#shift-deskripsi').val(shift?.deskripsi || '');
    $('#modal-shift').removeClass('hidden').addClass('flex');
}
function tutupModalShift() { $('#modal-shift').addClass('hidden').removeClass('flex'); }

// ===== Auto-Generate Roster Bulanan =====
function openModalGenerate() {
    Swal.fire({
        title: 'Auto-Generate Roster Bulanan',
        html: `
            <style>
                .gen-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; text-align:left; }
                .gen-grid .full { grid-column:1/-1; }
                .swal2-input.gen-in { margin:0; width:100%; font-size:12px; background:#fff; color:#0f172a; border:1px solid #cbd5e1; }
                .gen-label { display:block; text-align:left; font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:3px; }
                .gen-sec h4 { font-size:11px; font-weight:800; color:#b45309; text-align:left; margin:6px 0 4px; }
                .gen-sec { background:#fffbeb; border:1px solid #fde68a; border-radius:14px; padding:10px; margin-top:8px; display:grid; grid-template-columns:1fr 1fr; gap:10px; text-align:left; }
            </style>
            <div class="gen-grid">
                <div class="full">
                    <label class="gen-label">Jenis Karyawan</label>
                    <select id="gen-jenis" class="swal2-input gen-in">
                        <option value="semua">Semua Jenis Karyawan</option>
                        <option value="tetap">Karyawan Tetap</option>
                        <option value="kontrak">Karyawan Kontrak</option>
                    </select>
                </div>
                <div class="full">
                    <label class="gen-label">Karyawan</label>
                    <select id="gen-karyawan" class="swal2-input gen-in">
                        <option value="all">Semua Karyawan Aktif</option>
                        @foreach($karyawanList as $k) <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->nip }})</option> @endforeach
                    </select>
                </div>
                <div class="full">
                    <label class="gen-label">Pola Roster</label>
                    <select id="gen-pola" class="swal2-input gen-in">
                        <option value="6_1">6 Hari Kerja - 1 Off (Off Minggu)</option>
                        <option value="5_2">5 Hari Kerja - 2 Off (Off Sabtu-Minggu)</option>
                        <option value="rotasi_pagi_siang">Rotasi Pagi/Siang per Minggu</option>
                        <option value="2p_2s_2m_2off">2 Pagi - 2 Siang - 2 Malam - 2 Off</option>
                    </select>
                </div>
                <div><label class="gen-label">Bulan</label><select id="gen-bulan" class="swal2-input gen-in">@for($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ $i === now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}</option> @endfor</select></div>
                <div><label class="gen-label">Tahun</label><input id="gen-tahun" type="number" value="{{ now()->year }}" min="2024" max="2030" class="swal2-input gen-in"></div>
                <div><label class="gen-label">Shift Pagi/Kerja</label><select id="gen-shift-pagi" class="swal2-input gen-in">@foreach($shiftsList->where('is_off', 0) as $s) <option value="{{ $s->id }}">{{ $s->kode_shift }} • {{ $s->nama_shift }}</option> @endforeach</select></div>
                <div><label class="gen-label">Shift Off</label><select id="gen-shift-off" class="swal2-input gen-in">@foreach($shiftsList->where('is_off', 1) as $s) <option value="{{ $s->id }}">{{ $s->kode_shift }} • {{ $s->nama_shift }}</option> @endforeach</select></div>
            </div>
            <div class="gen-sec">
                <div>
                    <h4><i class="fa-solid fa-clock"></i> Karyawan Tetap</h4>
                    <label class="gen-label">Jam Masuk</label><input type="time" id="gen-jam-tetap-masuk" value="{{ $jamKerja['tetap']['masuk'] }}" class="swal2-input gen-in">
                    <label class="gen-label" style="margin-top:6px">Jam Pulang</label><input type="time" id="gen-jam-tetap-pulang" value="{{ $jamKerja['tetap']['pulang'] }}" class="swal2-input gen-in">
                </div>
                <div>
                    <h4><i class="fa-solid fa-clock"></i> Karyawan Kontrak</h4>
                    <label class="gen-label">Jam Masuk</label><input type="time" id="gen-jam-kontrak-masuk" value="{{ $jamKerja['kontrak']['masuk'] }}" class="swal2-input gen-in">
                    <label class="gen-label" style="margin-top:6px">Jam Pulang</label><input type="time" id="gen-jam-kontrak-pulang" value="{{ $jamKerja['kontrak']['pulang'] }}" class="swal2-input gen-in">
                </div>
                <p class="full text-[9px] text-slate-400">Jam ini diterapkan ke karyawan tetap & kontrak sesuai jenisnya saat generate. Karyawan dengan jam kerja custom diabaikan.</p>
            </div>
        `,
        width: 620,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-wand-magic-sparkles"></i> Generate Roster',
        confirmButtonColor: '#4f46e5',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-3xl shadow-2xl' },
        preConfirm: () => {
            const karyawan = $('#gen-karyawan').val();
            return {
                karyawan_all: karyawan,
                jenis: $('#gen-jenis').val(),
                pola: $('#gen-pola').val(),
                bulan: $('#gen-bulan').val(),
                tahun: $('#gen-tahun').val(),
                shift_pagi_id: $('#gen-shift-pagi').val(),
                shift_off_id: $('#gen-shift-off').val(),
                karyawan_ids: karyawan === 'all' ? 'all' : [karyawan],
                jam_tetap_masuk: $('#gen-jam-tetap-masuk').val(),
                jam_tetap_pulang: $('#gen-jam-tetap-pulang').val(),
                jam_kontrak_masuk: $('#gen-jam-kontrak-masuk').val(),
                jam_kontrak_pulang: $('#gen-jam-kontrak-pulang').val(),
            };
        },
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const d = result.value;
            d._token = $('meta[name="csrf-token"]').attr('content');
            d.action = 'generate_pola_bulanan';
            $.post("{{ route('admin.ajax.crud-shift') }}", d, function (res) {
                if (res.status === 'success')
                    Swal.fire({icon:'success', title:'Selesai', text:res.message, timer:1500, showConfirmButton:false}).then(() => location.reload());
                else Swal.fire({icon:'error', title:'Gagal', text:res.message});
            }, 'json').fail(x => Swal.fire({icon:'error', title:'Gagal', text: x.responseJSON?.message || 'Terjadi kesalahan server.'}));
        }
    });
}

$('#form-shift').on('submit', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const data = Object.fromEntries(new FormData(this).entries());
    data.action = id ? 'edit_shift' : 'tambah_shift';
    data.is_night_shift = $('#shift-night').prop('checked') ? 1 : 0;
    data.is_off = $('#shift-off').prop('checked') ? 1 : 0;
    if (id) data.id = id;
    ajaxPost("{{ route('admin.ajax.crud-shift') }}", data);
});

function hapusShift(id) {
    Swal.fire({icon:'warning', title:'Hapus shift ini?', showCancelButton:true}).then(r => {
        if (r.isConfirmed) ajaxPost("{{ route('admin.ajax.crud-shift') }}", { action: 'hapus_shift', id });
    });
}

// ===== Matriks Roster Bulanan =====


function filterRosterKaryawan() {
    const query = ($('#searchRosterKaryawan').val() || '').toLowerCase().trim();
    const deptFilter = ($('#filterDeptRoster').val() || 'all').toLowerCase();
    const total = $('.row-roster').length;
    let visibleCount = 0;
    $(query.length > 0 ? '#btnResetSearchRoster' : '#btnResetSearchRoster').toggleClass('hidden', query.length === 0);
    $('.row-roster').each(function () {
        const nama = ($(this).attr('data-nama') || '');
        const nip = ($(this).attr('data-nip') || '');
        const dept = ($(this).attr('data-dept') || '');
        const ok = (!query || nama.includes(query) || nip.includes(query)) && (deptFilter === 'all' || dept === deptFilter);
        $(this).toggle(ok);
        if (ok) visibleCount++;
    });
    $('#counterRosterText').text('Menampilkan ' + visibleCount + ' dari ' + total + ' Karyawan');
}

function resetFilterRoster() {
    $('#searchRosterKaryawan').val('');
    $('#filterDeptRoster').val('all');
    filterRosterKaryawan();
}

$('#searchRosterKaryawan').on('input', filterRosterKaryawan);
$('#filterDeptRoster').on('change', filterRosterKaryawan);

function openModalAssign(empId, tanggal) {
    const karyawanOpts = [];
    @foreach($karyawanList as $k) karyawanOpts.push({ id: {{ $k->id }}, t: '{{ $k->nama }} ({{ $k->nip }})' }); @endforeach
    const shiftOpts = [];
    @foreach($shiftsList as $s) shiftOpts.push({ id: {{ $s->id }}, t: '{{ $s->kode_shift }} • {{ $s->nama_shift }}' }); @endforeach

    Swal.fire({
        title: 'Assign Roster Satu Tanggal',
        html: `
            <div style="text-align:left; display:grid; gap:10px;">
                <label style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:2px; display:block;">Karyawan</label>
                <select id="as-karyawan" style="width:100%; margin:0; padding:10px; border-radius:12px; border:1px solid #cbd5e1; font-size:12px; background:#fff;">
                    ${karyawanOpts.map(k => `<option value="${k.id}" ${k.id == empId ? 'selected' : ''}>${k.t}</option>`).join('')}
                </select>
                <label style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:2px; display:block;">Shift</label>
                <select id="as-shift" style="width:100%; margin:0; padding:10px; border-radius:12px; border:1px solid #cbd5e1; font-size:12px; background:#fff;">
                    ${shiftOpts.map(s => `<option value="${s.id}">${s.t}</option>`).join('')}
                </select>
                <label style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:2px; display:block;">Tanggal</label>
                <input id="as-tanggal" type="date" value="${tanggal || new Date().toISOString().slice(0,10)}" style="width:100%; margin:0; padding:10px; border-radius:12px; border:1px solid #cbd5e1; font-size:12px;">
                <label style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:2px; display:block;">Catatan (opsional)</label>
                <input id="as-catatan" type="text" style="width:100%; margin:0; padding:10px; border-radius:12px; border:1px solid #cbd5e1; font-size:12px;">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-save"></i> Simpan Roster',
        confirmButtonColor: '#2563eb',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-3xl shadow-2xl' },
        preConfirm: () => {
            if (!Swal.getPopup().querySelector('#as-karyawan').value || !Swal.getPopup().querySelector('#as-shift').value) {
                Swal.showValidationMessage('Lengkapi karyawan & shift');
                return false;
            }
            return {
                karyawan_id: Swal.getPopup().querySelector('#as-karyawan').value,
                shift_id: Swal.getPopup().querySelector('#as-shift').value,
                tanggal: Swal.getPopup().querySelector('#as-tanggal').value,
                catatan: Swal.getPopup().querySelector('#as-catatan').value,
                action: 'assign_roster_single',
                _token: $('meta[name="csrf-token"]').attr('content'),
            };
        },
    }).then((result) => {
        if (result.isConfirmed) {
            ajaxPost("{{ route('admin.ajax.crud-shift') }}", result.value);
        }
    });
}

// ===== Seeder Absensi =====
function openModalRole(role) {
    $('#form-role')[0].reset();
    $('#form-role').data('id', role?.id || '');
    $('#modal-role-title').text(role ? 'Edit Role: ' + role.nama_role : 'Tambah Role');
    $('#wrap-role-kode').toggle(!role);
    // input wajib yang disembunyikan tidak boleh memblokir validasi form saat edit
    $('#role-kode').prop('disabled', !!role);
    $('#role-nama').val(role?.nama_role || '');
    $('#role-deskripsi').val(role?.deskripsi || '');
    const izin = role?.izin_akses || [];
    $('.perm-cb').each(function () { $(this).prop('checked', izin.includes($(this).val())); });
    $('#modal-role').removeClass('hidden').addClass('flex');
}
function tutupModalRole() { $('#modal-role').addClass('hidden').removeClass('flex'); }

$('#form-role').on('submit', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const perms = $('.perm-cb:checked').map(function () { return this.value; }).get();
    const data = {
        action: id ? 'edit' : 'tambah',
        nama_role: $('#role-nama').val(),
        deskripsi: $('#role-deskripsi').val(),
        izin_akses: JSON.stringify(perms),
    };
    if (id) data.id = id; else data.kode_role = $('#role-kode').val();
    ajaxPost("{{ route('admin.ajax.crud-role') }}", data);
});
</script>
@endpush
