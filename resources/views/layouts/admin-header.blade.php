<header class="bg-white/95 backdrop-blur border-b border-slate-200 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-slate-900 rounded-xl text-white shadow-sm">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-slate-900 leading-tight">Absenplus+ <span class="text-indigo-600">Admin</span></h1>
                <p class="text-[10px] text-slate-500">{{ auth('karyawan')->user()->nama }} &bull; {{ auth('karyawan')->user()->role->nama_role ?? '' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('absen') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-fingerprint text-blue-600"></i> Portal Absensi
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-xl text-xs font-semibold transition" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</header>
