    <!-- Statistik -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
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
        <form id="form-filter-logs" class="flex flex-wrap items-end gap-3 p-4 rounded-2xl bg-gradient-to-r from-slate-50 to-blue-50/50 border border-slate-200">
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
            <button type="button" onclick="resetFilterLogs()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 cursor-pointer transition">Reset <i class="fa-solid fa-rotate-left"></i></button>
        </form>

        @if($presensiHariIni->isEmpty())
        <div class="p-10 text-center rounded-2xl border-2 border-dashed border-slate-200">
            <i class="fa-regular fa-folder-open text-4xl text-slate-200 block mb-3"></i>
            <p class="text-xs font-bold text-slate-400">Tidak ada data absensi pada periode ini</p>
            <p class="text-[11px] text-slate-300 mt-1">Coba ubah rentang tanggal atau departemen</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
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
                @php $catatanSaya = $catatanAbsensi->get($row->karyawan_id); @endphp
                @if($catatanSaya && $catatanSaya->isNotEmpty())
                <div class="mt-2 space-y-1">
                    @foreach($catatanSaya as $ct)
                    <div class="flex items-start gap-1.5 p-2 rounded-xl bg-amber-50 border border-amber-200 text-[10px] text-amber-800">
                        <i class="fa-solid fa-file-lines mt-0.5 shrink-0"></i>
                        <div class="min-w-0 flex-1">
                            <span class="font-bold font-mono">{{ $ct->tanggal->translatedFormat('d M Y') }}</span> — <span class="break-words">{{ $ct->catatan }}</span>
                        </div>
                        <button type="button" onclick="editCatatanAbsen({{ $row->karyawan_id }}, '{{ $row->nama }}', '{{ $ct->tanggal->toDateString() }}', @js($ct->catatan))" class="shrink-0 w-6 h-6 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-700 flex items-center justify-center cursor-pointer" title="Edit catatan"><i class="fa-solid fa-pen text-[9px]"></i></button>
                    </div>
                    @endforeach
                </div>
                @endif
                <button type="button" onclick="editCatatanAbsen({{ $row->karyawan_id }}, '{{ $row->nama }}', '{{ \Illuminate\Support\Carbon::parse($row->log_tanggal)->toDateString() }}', '')" class="mt-2 w-full py-1.5 rounded-xl border border-dashed border-slate-200 hover:border-blue-300 hover:bg-blue-50 text-[10px] font-bold text-slate-400 hover:text-blue-600 transition cursor-pointer">
                    <i class="fa-solid fa-plus"></i> Catatan Admin (Sistem Eror / Lupa Absen)
                </button>
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
        <form method="GET" action="{{ route('admin.rekap') }}" class="grid grid-cols-2 sm:grid-cols-4 gap-5">
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
        <div class="flex flex-wrap gap-3">
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
                            <button type="button" onclick="refreshRoster({{ $rosterPrev['bulan'] }}, {{ $rosterPrev['tahun'] }})" class="p-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl text-slate-700 transition shadow-sm" title="Bulan Sebelumnya">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <form onsubmit="event.preventDefault(); refreshRoster();" class="flex items-center gap-2">
                                <input type="hidden" name="dari" value="{{ $logDari }}"><input type="hidden" name="sampai" value="{{ $logSampai }}">
                                <select name="roster_bulan" onchange="refreshRoster(this.value, null)" class="bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-1.5 text-xs font-bold focus:outline-none focus:border-indigo-500">
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m === $rosterBulan ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                                    @endfor
                                </select>
                                <select name="roster_tahun" onchange="refreshRoster(null, this.value)" class="bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-1.5 text-xs font-bold focus:outline-none focus:border-indigo-500">
                                    @for($y = 2024; $y <= 2028; $y++)
                                    <option value="{{ $y }}" {{ $y === $rosterTahun ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </form>
                            <button type="button" onclick="refreshRoster({{ $rosterNext['bulan'] }}, {{ $rosterNext['tahun'] }})" class="p-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl text-slate-700 transition shadow-sm" title="Bulan Berikutnya">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
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
                    <div id="roster-matrix-wrap" class="overflow-x-auto custom-scroll rounded-2xl border border-slate-200">
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
