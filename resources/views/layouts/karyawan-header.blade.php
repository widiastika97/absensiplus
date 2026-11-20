<header class="bg-white/90 backdrop-blur border-b border-slate-200 sticky top-0 z-30 shadow-sm">
    <div class="max-w-6xl mx-auto px-3 sm:px-4 py-3 sm:py-3.5 flex items-center justify-between">
        <div class="flex items-center gap-2.5 sm:gap-3">
            <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white text-base sm:text-lg shadow-md shadow-blue-600/20 shrink-0">
                <i class="fa-solid fa-fingerprint"></i>
            </div>
            <div>
                <h1 class="text-xs sm:text-sm font-bold text-slate-900 leading-tight">Absenplus+</h1>
                <p class="text-[10px] sm:text-[11px] text-slate-500">Karyawan</p>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="text-right">
                <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                    <p class="text-xs font-bold text-slate-800 truncate max-w-[130px] sm:max-w-[200px]">{{ auth('karyawan')->user()->nama }}</p>
                </div>
                <p class="text-[10px] text-slate-500 hidden sm:block">{{ auth('karyawan')->user()->nip }} &bull; {{ auth('karyawan')->user()->departemen }}</p>
            </div>

            @if(auth('karyawan')->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="px-2.5 sm:px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition shadow-sm" title="Panel Administrator">
                    <i class="fa-solid fa-gauge-high"></i> <span class="hidden sm:inline">Admin</span>
                </a>
            @endif

            <nav class="hidden md:flex items-center gap-1 text-xs font-semibold">
                <a href="{{ route('absen') }}" @class(['px-3 py-1.5 rounded-lg transition', request()->routeIs('absen') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'])>
                    <i class="fa-solid fa-fingerprint"></i> Absen
                </a>
                <a href="{{ route('riwayat') }}" @class(['px-3 py-1.5 rounded-lg transition', request()->routeIs('riwayat') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'])>
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat
                </a>
                @canany(['pengajuan_cuti_karyawan', 'pengajuan_cuti_pribadi'])
                <a href="{{ route('pengajuan-cuti') }}" @class(['px-3 py-1.5 rounded-lg transition', request()->routeIs('pengajuan-cuti') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'])>
                    <i class="fa-solid fa-envelope-open-text"></i> Cuti
                </a>
                @endcanany
                <a href="{{ route('profil') }}" @class(['px-3 py-1.5 rounded-lg transition', request()->routeIs('profil') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'])>
                    <i class="fa-solid fa-user"></i> Profil
                </a>
            </nav>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-2.5 sm:px-3 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition" title="Keluar dari Aplikasi">
                    <i class="fa-solid fa-right-from-bracket"></i> <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </div>
    <!-- Mobile nav -->
    </header>

<!-- Mobile Bottom Navigation -->
<nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
    <div class="flex items-stretch divide-x divide-slate-100">
        <a href="{{ route('absen') }}" @class([
            'flex flex-1 flex-col items-center justify-center py-2.5 text-[10px] font-bold transition',
            request()->routeIs('absen') ? 'text-blue-600 bg-blue-50' : 'text-slate-500'
        ])><i class="fa-solid fa-fingerprint text-base"></i> Absen</a>
        <a href="{{ route('riwayat') }}" @class([
            'flex flex-1 flex-col items-center justify-center py-2.5 text-[10px] font-bold transition',
            request()->routeIs('riwayat') ? 'text-blue-600 bg-blue-50' : 'text-slate-500'
        ])><i class="fa-solid fa-clock-rotate-left text-base"></i> Riwayat</a>
        @canany(['pengajuan_cuti_karyawan', 'pengajuan_cuti_pribadi'])
        <a href="{{ route('pengajuan-cuti') }}" @class([
            'flex flex-1 flex-col items-center justify-center py-2.5 text-[10px] font-bold transition',
            request()->routeIs('pengajuan-cuti') ? 'text-blue-600 bg-blue-50' : 'text-slate-500'
        ])><i class="fa-solid fa-envelope-open-text text-base"></i> Cuti</a>
        @endcanany
        <a href="{{ route('profil') }}" @class([
            'flex flex-1 flex-col items-center justify-center py-2.5 text-[10px] font-bold transition',
            request()->routeIs('profil') ? 'text-blue-600 bg-blue-50' : 'text-slate-500'
        ])><i class="fa-solid fa-user text-base"></i> Profil</a>
    </div>
</nav>