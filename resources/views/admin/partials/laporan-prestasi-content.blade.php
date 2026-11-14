    <!-- Filter Periode -->
    @if(empty($hideFilter))
    <form method="GET" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-end gap-3">
        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Dari</label>
            <input type="date" name="dari" value="{{ $dari }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
        </div>
        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Sampai</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-filter"></i> Terapkan</button>
        <a href="{{ route('laporan-prestasi') }}" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200">Reset</a>
    </form>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        @php
        $panels = [
        ['Siswa Berprestasi', 'fa-trophy', 'emerald', $siswaBerprestasi, 'prestasi_siswa'],
        ['Siswa Paling Sering Pelanggaran', 'fa-triangle-exclamation', 'rose', $siswaPelanggar, 'pelanggaran_siswa'],
        ['Guru Berprestasi', 'fa-medal', 'teal', $guruBerprestasi, 'prestasi_guru'],
        ['Guru Paling Sering Pelanggaran', 'fa-user-slash', 'orange', $guruPelanggar, 'pelanggaran_guru'],
        ];
        @endphp

        @foreach($panels as [$judul, $icon, $warna, $rows, $tipe])
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid {{ $icon }} text-{{ $warna }}-600"></i>
                <h2 class="text-sm font-bold text-slate-900">{{ $judul }}</h2>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="p-2.5">#</th>
                        <th class="p-2.5">Nama</th>
                        <th class="p-2.5">{{ str_starts_with($tipe, 'prestasi_siswa') || $tipe === 'pelanggaran_siswa' ? 'Kelas' : 'NIP' }}</th>
                        <th class="p-2.5 text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($rows as $i => $r)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-2.5 font-bold {{ $i < 3 ? 'text-amber-500' : 'text-slate-400' }}">{{ $i + 1 }}</td>
                        <td class="p-2.5 font-bold text-slate-800">{{ $r->nama }}</td>
                        <td class="p-2.5 text-slate-500 font-mono">{{ $r->kelas ?? $r->nip ?? '-' }}</td>
                        <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded font-bold bg-{{ $warna }}-50 text-{{ $warna }}-700">{{ $r->total }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-400">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endforeach

        <!-- Guru paling rajin melapor -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-bullhorn text-blue-600"></i>
                <h2 class="text-sm font-bold text-slate-900">Guru Paling Rajin Melapor</h2>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-100">
                    <tr><th class="p-2.5">#</th><th class="p-2.5">Nama</th><th class="p-2.5 text-center">Prestasi</th><th class="p-2.5 text-center">Pelanggaran</th><th class="p-2.5 text-center">Total Lapor</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($guruRajinMelapor as $i => $r)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-2.5 font-bold {{ $i < 3 ? 'text-amber-500' : 'text-slate-400' }}">{{ $i + 1 }}</td>
                        <td class="p-2.5 font-bold text-slate-800">{{ $r->pelapor?->nama ?? '-' }} <span class="text-slate-400 font-mono">{{ $r->pelapor?->nip }}</span></td>
                        <td class="p-2.5 text-center text-emerald-600 font-bold">{{ $r->total_prestasi }}</td>
                        <td class="p-2.5 text-center text-rose-600 font-bold">{{ $r->total_pelanggaran }}</td>
                        <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded font-bold bg-blue-50 text-blue-700">{{ $r->total_catatan }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-400">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Murid paling rajin -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-fire text-amber-500"></i>
                <h2 class="text-sm font-bold text-slate-900">Murid Paling Rajin</h2>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-100">
                    <tr><th class="p-2.5">#</th><th class="p-2.5">Nama</th><th class="p-2.5">Kelas</th><th class="p-2.5 text-center">Prestasi</th><th class="p-2.5 text-center">Total Catatan</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($muridRajin as $i => $r)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-2.5 font-bold {{ $i < 3 ? 'text-amber-500' : 'text-slate-400' }}">{{ $i + 1 }}</td>
                        <td class="p-2.5 font-bold text-slate-800">{{ $r->nama }}</td>
                        <td class="p-2.5 text-slate-500">{{ $r->kelas ?? '-' }}</td>
                        <td class="p-2.5 text-center text-emerald-600 font-bold">{{ $r->total_prestasi }}</td>
                        <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded font-bold bg-amber-50 text-amber-700">{{ $r->total_catatan }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-400">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
