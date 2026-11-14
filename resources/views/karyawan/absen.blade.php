@extends('layouts.app')

@section('title', 'Portal Absensi Karyawan')

@section('content')
@include('layouts.karyawan-header')

<main class="max-w-6xl mx-auto px-2.5 sm:px-4 py-4 sm:py-6 pb-28 sm:pb-28 space-y-5">

    <!-- 1. LIVE CLOCK + STATUS -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0"><i class="fa-solid fa-clock"></i></div>
            <div>
                <div id="live-clock" class="text-lg sm:text-xl font-bold font-mono text-slate-900">00:00:00 WITA</div>
                <div class="text-[10px] sm:text-xs text-slate-500">{{ now()->translatedFormat('d F Y') }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold {{ $sudahMasuk ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">Masuk {{ $sudahMasuk ? substr($logMasuk->jam,0,5) : '—' }}</span>
            <span class="px-2.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold {{ $sudahPulang ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">Pulang {{ $sudahPulang ? substr($logPulang->jam,0,5) : '—' }}</span>
        </div>
    </div>

    <!-- 2. BUKTI PRESENSI (hanya tampil setelah presensi masuk) -->
    @if($logMasuk || $logPulang)
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-camera-retro text-blue-600"></i> Bukti Presensi Hari Ini</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
            <div class="flex flex-col gap-1">
                <p class="text-xs sm:text-sm font-bold text-emerald-700 flex items-center gap-1.5"><i class="fa-solid fa-arrow-right-to-bracket"></i> {{ $logMasuk ? 'Bukti Masuk — '.substr($logMasuk->jam,0,5).' WITA' : 'Bukti Masuk — belum' }}</p>
                @if($logMasuk && $logMasuk->foto_selfie)
                <img src="/storage/selfie/{{ $logMasuk->foto_selfie }}" onclick="perbesarFoto(this.src)" class="w-full aspect-square rounded-xl border border-slate-200 cursor-pointer hover:opacity-90 transition shadow-sm object-cover" alt="Swafoto masuk">
                @else
                <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-slate-100 to-emerald-50 border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400">
    <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center mb-2"><i class="fa-solid fa-user text-2xl text-emerald-500"></i></div>
    <span class="text-xs font-bold">Belum Ada Bukti</span>
    <span class="text-[10px] font-medium text-slate-400 mt-0.5">Ambil swafoto saat presensi masuk</span>
</div>
                @endif
            </div>
            <div class="flex flex-col gap-1">
                <p class="text-xs sm:text-sm font-bold text-blue-700 flex items-center gap-1.5"><i class="fa-solid fa-arrow-right-from-bracket"></i> {{ $logPulang ? 'Bukti Pulang — '.substr($logPulang->jam,0,5).' WITA' : 'Bukti Pulang — belum' }}</p>
                @if($logPulang && $logPulang->foto_selfie)
                <img src="/storage/selfie/{{ $logPulang->foto_selfie }}" onclick="perbesarFoto(this.src)" class="w-full aspect-square rounded-xl border border-slate-200 cursor-pointer hover:opacity-90 transition shadow-sm object-cover" alt="Swafoto pulang">
                @else
                <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-slate-100 to-blue-50 border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400">
    <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center mb-2"><i class="fa-solid fa-camera text-2xl text-blue-500"></i></div>
    <span class="text-xs font-bold">Belum Ada Bukti</span>
    <span class="text-[10px] font-medium text-slate-400 mt-0.5">Ambil swafoto saat presensi pulang</span>
</div>
                
            </div>
        </div>
    </div>
    @endif
    @endif

    <!-- 3. HIGHLIGHT: RADAR GPS + TOMBOL -->
    <div class="bg-white p-4 sm:p-6 rounded-3xl border border-slate-200 shadow-sm space-y-5">
        @if($isShiftOff)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3 text-amber-900">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg shrink-0"><i class="fa-solid fa-mug-hot"></i></div>
            <div><h4 class="text-xs sm:text-sm font-bold">Hari ini jadwal libur ({{ $namaShiftAktif }})</h4><p class="text-[11px] text-amber-700">Anda tidak diwajibkan presensi.</p></div>
        </div>
        @endif
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 bg-gradient-to-r from-slate-50 to-blue-50/40 border border-slate-200 rounded-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-base shrink-0"><i class="fa-solid fa-location-dot"></i></div>
                <div>
                    <div class="flex items-center gap-2"><h3 class="text-xs sm:text-sm font-bold text-slate-900">Radar Geofencing</h3><span id="radar-badge-main" class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Membaca GPS...</span></div>
                    <p id="radar-desc-main" class="text-[11px] text-slate-500">Verifikasi lokasi Anda terhadap titik kantor...</p>
                </div>
            </div>
            <button type="button" id="btn-refresh-gps" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm active:scale-95 cursor-pointer flex items-center gap-1.5 transition"><i class="fa-solid fa-arrows-rotate text-blue-600"></i> Refresh GPS</button>
        </div>
        <div class="grid grid-cols-1 gap-4">
            @unless($sudahMasuk)
            <button type="button" id="btn-trigger-absen" data-tipe="masuk" class="p-5 rounded-2xl border-2 border-emerald-300 bg-emerald-50 hover:bg-emerald-100 cursor-pointer transition active:scale-98 text-left">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-lg shadow-emerald-600/20"><i class="fa-solid fa-fingerprint"></i></div>
                    <div><p class="text-sm font-bold text-emerald-800">Presensi Masuk</p><p class="text-[11px] text-emerald-600">Klik untuk ambil swafoto & absen</p></div>
                </div>
            </button>
            @endunless
            @if($sudahMasuk && !$sudahPulang)
            <button type="button" id="btn-trigger-pulang" class="p-5 rounded-2xl border-2 border-blue-300 bg-blue-50 hover:bg-blue-100 cursor-pointer transition active:scale-98 text-left">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-600/20"><i class="fa-solid fa-person-walking-arrow-right"></i></div>
                    <div><p class="text-sm font-bold text-blue-800">Presensi Pulang</p><p class="text-[11px] text-blue-600">Klik untuk ambil swafoto & pulang</p></div>
                </div>
            </button>
            @endif
            @if($sudahMasuk && $sudahPulang)
            <div class="p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 flex items-center gap-3 text-slate-500">
                <div class="w-12 h-12 rounded-2xl bg-slate-200 text-slate-500 flex items-center justify-center text-xl"><i class="fa-solid fa-circle-check"></i></div>
                <div><p class="text-sm font-bold text-slate-700">Absensi hari ini lengkap</p><p class="text-[11px]">Masuk {{ $logMasuk ? substr($logMasuk->jam,0,5) : '-' }} • Pulang {{ $logPulang ? substr($logPulang->jam,0,5) : '-' }} WITA</p></div>
            </div>
            @endif
        </div>
    </div>


    <!-- 4. KONTEKSTUAL: CATATAN PRESTASI/PELANGGARAN -->
    @if($sudahMasuk && !$sudahPulang)
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3" id="panel-catatan">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-trophy text-emerald-600"></i> Catatan Prestasi & Pelanggaran</h3>
        <p class="text-[11px] text-slate-500">Wajib melampirkan foto bukti. Hanya tersedia selama jam kerja (setelah presensi masuk).</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button type="button" onclick="bukaModalCatatan('prestasi')" class="p-4 rounded-2xl border-2 border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-left transition active:scale-98 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg"><i class="fa-solid fa-trophy"></i></div>
                    <div><p class="text-sm font-bold text-emerald-800">Tambah Prestasi</p><p class="text-[11px] text-emerald-600">Siswa{{ $isAdminPengguna ? ' / Guru' : '' }}</p></div>
                </div>
            </button>
            <button type="button" onclick="bukaModalCatatan('pelanggaran')" class="p-4 rounded-2xl border-2 border-rose-300 bg-rose-50 hover:bg-rose-100 text-left transition active:scale-98 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div><p class="text-sm font-bold text-rose-800">Tambah Pelanggaran</p><p class="text-[11px] text-rose-600">Siswa{{ $isAdminPengguna ? ' / Guru' : '' }}</p></div>
                </div>
            </button>
            @can('laporan_prestasi_pelanggaran')
            <a href="{{ route('laporan-prestasi') }}" class="sm:col-span-2 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300 transition cursor-pointer shadow-sm">
                <i class="fa-solid fa-chart-bar"></i> Lihat Laporan Prestasi & Pelanggaran
            </a>
            @endcan
        </div>
    </div>
    @elseif($sudahMasuk && $sudahPulang)
    <p class="text-[11px] text-slate-500 bg-white border border-slate-200 rounded-2xl p-3 shadow-sm"><i class="fa-solid fa-circle-check text-emerald-500"></i> Absensi hari ini sudah lengkap — pengisian prestasi/pelanggaran hanya tersedia selama jam kerja.</p>
    @else
    <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded-2xl p-3 shadow-sm"><i class="fa-solid fa-circle-info"></i> Anda dapat menambahkan Prestasi/Pelanggaran setelah melakukan presensi masuk hari ini.</p>
    @endif

    <!-- 5. PELENGKAP -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-slate-50/70 rounded-xl border border-slate-100 p-3"><p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide flex items-center gap-1"><i class="fa-solid fa-business-time text-blue-500"></i> Shift Hari Ini</p><p class="text-base font-bold text-slate-800 mt-1">{{ $namaShiftAktif }}</p></div>
        <div class="bg-slate-50/70 rounded-xl border border-slate-100 p-3"><p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide flex items-center gap-1"><i class="fa-solid fa-calendar-days text-emerald-500"></i> Sisa Cuti {{ now()->year }}</p><p class="text-base font-bold text-slate-800 mt-1">{{ $kuota['remaining_quota'] }} / {{ $kuota['total_quota'] }} hari</p></div>
        <div class="bg-slate-50/70 rounded-xl border border-slate-100 p-3"><p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide flex items-center gap-1"><i class="fa-solid fa-hourglass-half text-rose-500"></i> Cuti Pending</p><p class="text-base font-bold text-slate-800 mt-1">{{ $totalCutiPending }} pengajuan</p></div>
    </div>

    <!-- Agenda libur -->
    @if($liburBulanIni->isNotEmpty())
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-xs font-bold text-slate-900 mb-3 flex items-center gap-2"><i class="fa-solid fa-calendar-days text-amber-600"></i> Agenda Libur Bulan Ini</h3>
        <div class="space-y-2">
            @foreach($liburBulanIni as $libur)
            <div class="flex items-center justify-between p-3 bg-amber-50 border border-amber-100 rounded-xl">
                <p class="text-xs font-bold text-amber-900">{{ $libur->nama_libur }}</p>
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-amber-100 text-amber-700">{{ $libur->tanggal_mulai->translatedFormat('d M') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</main>

<!-- Modal Presensi Selfie -->
<div id="modal-absen" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900" id="modal-title">Presensi Masuk</h3>
                <p class="text-[11px] text-slate-500">Ambil swafoto dengan wajah di tengah lingkaran panduan</p>
            </div>
            <button type="button" id="btn-close-modal" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-5 space-y-4">
            <div id="modal-radar-box" class="p-3 rounded-2xl border border-slate-200 bg-slate-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 text-sm"><i class="fa-solid fa-location-dot"></i></div>
                <div class="min-w-0 flex-1">
                    <span id="modal-radar-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Membaca GPS...</span>
                    <p id="modal-radar-details" class="text-[10px] text-slate-500 mt-1 truncate">Mengambil koordinat...</p>
                </div>
                <button type="button" id="btn-modal-refresh-gps" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 cursor-pointer"><i class="fa-solid fa-arrows-rotate"></i></button>
            </div>
            <div id="modal-radius-warning" class="hidden p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-semibold">Anda harus berada dalam radius kantor untuk dapat melakukan presensi.</div>
            <div class="relative aspect-square bg-slate-900 rounded-2xl overflow-hidden">
                <video id="webcam-video" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover" style="transform: scaleX(-1);"></video>
                <img id="preview-photo" class="absolute inset-0 w-full h-full object-cover hidden" alt="Pratinjau">
                <div id="face-guide" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-48 h-48 rounded-full border-2 border-white/60 border-dashed"></div>
                </div>
                <canvas id="snapshot-canvas" class="hidden"></canvas>
            </div>
            <div id="modal-snap-controls" class="flex gap-2.5">
                <button type="button" id="btn-snap-modal" class="flex-1 min-h-[46px] py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-md shadow-blue-600/20 active:scale-98 cursor-pointer"><i class="fa-solid fa-camera"></i> Ambil Foto</button>
            </div>
            <div id="modal-submit-controls" class="hidden gap-2.5">
                <button type="button" id="btn-retake-modal" class="flex-1 min-h-[46px] py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer"><i class="fa-solid fa-camera-rotate"></i> Foto Ulang</button>
                <button type="button" id="btn-submit-modal" class="flex-1 min-h-[46px] py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20 active:scale-98 cursor-pointer"><i class="fa-solid fa-paper-plane"></i> Kirim Absensi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catat Prestasi/Pelanggaran -->
<div id="modal-catatan" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl max-h-[92vh] overflow-y-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
            <div>
                <h3 class="text-sm font-bold text-slate-900" id="catatan-title">Tambah Prestasi</h3>
                <p class="text-[11px] text-slate-500">Isi data & unggah foto bukti</p>
            </div>
            <button type="button" onclick="tutupModalCatatan()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="form-catatan" class="p-5 space-y-3" enctype="multipart/form-data">
            <input type="hidden" name="kategori" id="catatan-kategori" value="prestasi">
            <div>
                <label class="text-[11px] font-bold text-slate-500 uppercase">Subjek</label>
                <select name="subjek" id="catatan-subjek" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                    <option value="siswa">Siswa</option>
                    @if(auth('karyawan')->user()->isAdmin())<option value="guru">Guru (Admin)</option>@endif
                </select>
            </div>
            <div id="catatan-wrap-siswa">
                <label class="text-[11px] font-bold text-slate-500 uppercase">Siswa</label>
                <select name="siswa_id" id="catatan-siswa" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                    <option value="">— Pilih Siswa —</option>
                    @foreach($siswaList ?? [] as $s)
                    <option value="{{ $s->id }}">{{ $s->nis }} • {{ $s->nama }}{{ $s->kelas ? ' ('.$s->kelas.')' : '' }}</option>
                    @endforeach
                </select>
                <input type="text" name="nama_manual" placeholder="atau isi nama manual" class="w-full mt-2 px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div id="catatan-wrap-guru" class="hidden">
                <label class="text-[11px] font-bold text-slate-500 uppercase">Guru</label>
                <select name="guru_id" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                    <option value="">— Pilih Guru —</option>
                </select>
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-500 uppercase">Judul *</label>
                <input type="text" name="judul" required id="catatan-judul" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Tanggal *</label>
                    <input type="date" name="tanggal" required value="{{ now()->toDateString() }}" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Keterangan</label>
                    <input type="text" name="keterangan" class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-500 uppercase">Foto Bukti *</label>
                <input type="file" name="foto_bukti" id="catatan-foto" accept="image/jpeg,image/png,image/webp" required class="w-full mt-1 px-3 py-2 rounded-xl border border-slate-200 text-xs file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-slate-900 file:text-white file:text-[11px] file:font-bold file:cursor-pointer cursor-pointer">
                <img id="catatan-preview" class="hidden mt-2 max-h-36 rounded-xl border border-slate-200" alt="Preview">
            </div>
            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold cursor-pointer"><i class="fa-solid fa-floppy-disk"></i> Simpan Catatan</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function perbesarFoto(url) {
    Swal.fire({ imageUrl: url, imageAlt: 'Swafoto Absensi', confirmButtonText: 'Tutup', confirmButtonColor: '#2563eb', customClass: { popup: 'rounded-3xl' } });
}

// ===== Catat Prestasi/Pelanggaran =====
function bukaModalCatatan(kategori) {
    $('#catatan-title').text(kategori === 'prestasi' ? 'Tambah Prestasi' : 'Tambah Pelanggaran');
    $('#catatan-preview').addClass('hidden');
    $('#form-catatan')[0].reset();
    $('#catatan-kategori').val(kategori);
    $('#modal-catatan').removeClass('hidden').addClass('flex');
}
function tutupModalCatatan() { $('#modal-catatan').addClass('hidden').removeClass('flex'); }

$('#catatan-subjek').change(function () {
    const isGuru = $(this).val() === 'guru';
    $('#catatan-wrap-siswa').toggleClass('hidden', isGuru);
    $('#catatan-wrap-guru').toggleClass('hidden', !isGuru);
});

$('#catatan-foto').change(function () {
    const f = this.files?.[0];
    if (f && f.type.startsWith('image/')) {
        $('#catatan-preview').attr('src', URL.createObjectURL(f)).removeClass('hidden');
    } else {
        $('#catatan-preview').addClass('hidden');
    }
});

$('#form-catatan').on('submit', function (e) {
    e.preventDefault();
    if (!document.getElementById('catatan-foto').files.length) {
        Swal.fire({icon:'warning', title:'Foto bukti wajib diunggah'}); return;
    }
    const fd = new FormData(this);
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
    $.ajax({
        url: "{{ route('ajax.submit-prestasi-pelanggaran') }}",
        type: 'POST', data: fd, processData: false, contentType: false, dataType: 'json',
        success: res => {
            if (res.status === 'success')
                Swal.fire({icon:'success', title:'Tersimpan', text:res.message, timer:1600, showConfirmButton:false}).then(() => { tutupModalCatatan(); $('#form-catatan')[0].reset(); });
            else Swal.fire({icon:'error', title:'Gagal', text:res.message});
        },
        error: xhr => Swal.fire({icon:'error', title:'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan.'})
    });
});

// ===== Presensi Masuk & Pulang =====
$(function () {
    let activeAttendanceType = 'masuk';
    let currentLat = null, currentLng = null, capturedImage = null, isWithinRadius = false;
    let webcamStream = null;
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    setInterval(function () {
        $('#live-clock').text(new Date().toLocaleTimeString('id-ID') + ' WITA');
    }, 1000);

    function triggerGPS(callback) {
        if (!navigator.geolocation) {
            updateRadarUI(false, null, 0, 0, 'Browser tidak mendukung GPS');
            return;
        }
        navigator.geolocation.getCurrentPosition(function (pos) {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;
            $.ajax({
                url: "{{ route('ajax.check-radius') }}",
                type: 'POST',
                data: { latitude: currentLat, longitude: currentLng, _token: CSRF },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        isWithinRadius = res.is_within_radius;
                        updateRadarUI(isWithinRadius, res.current_location ? res.current_location.nama_lokasi : 'Kantor', res.distance_meters, res.allowed_radius);
                    } else updateRadarUI(false, null, 0, 0, res.message);
                    if (typeof callback === 'function') callback();
                },
                error: function () {
                    updateRadarUI(false, null, 0, 0, 'Gagal terhubung ke server');
                    if (typeof callback === 'function') callback();
                }
            });
        }, function (err) {
            updateRadarUI(false, null, 0, 0, 'Izin GPS ditolak. Silakan aktifkan izin lokasi.');
            if (typeof callback === 'function') callback();
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    function updateRadarUI(isValid, officeName, distance, allowedRadius, errorMsg = '') {
        if (errorMsg) {
            $('#radar-badge-main, #modal-radar-status-badge').html('GPS Error')
                .removeClass('bg-emerald-50 text-emerald-700 border-emerald-200 bg-rose-50 text-rose-700 border-rose-200').addClass('bg-amber-50 text-amber-700 border-amber-200');
            $('#radar-desc-main, #modal-radar-details').text(errorMsg);
            $('#modal-radius-warning').removeClass('hidden');
            $('#btn-submit-modal').prop('disabled', true);
            return;
        }
        if (isValid) {
            $('#radar-badge-main, #modal-radar-status-badge').html('DALAM RADIUS')
                .removeClass('bg-amber-50 text-amber-700 border-amber-200 bg-rose-50 text-rose-700 border-rose-200 animate-pulse').addClass('bg-emerald-50 text-emerald-700 border-emerald-200');
            $('#radar-desc-main').html('Terdeteksi di <b>' + officeName + '</b> • Jarak: ' + distance + ' m');
            $('#modal-radar-details').html('Terdeteksi di <b>' + officeName + '</b> • Jarak: ' + distance + ' m');
            $('#modal-radius-warning').addClass('hidden');
            $('#btn-submit-modal').prop('disabled', false);
        } else {
            $('#radar-badge-main, #modal-radar-status-badge').html('DI LUAR RADIUS')
                .removeClass('bg-amber-50 text-amber-700 border-amber-200 bg-emerald-50 text-emerald-700 border-emerald-200 animate-pulse').addClass('bg-rose-50 text-rose-700 border-rose-200');
            $('#radar-desc-main').html('Jarak <b>' + distance + ' m</b> dari ' + (officeName || 'Kantor'));
            $('#modal-radius-warning').removeClass('hidden');
            $('#btn-submit-modal').prop('disabled', true);
        }
    }

    triggerGPS();
    $('#btn-refresh-gps, #btn-modal-refresh-gps').click(function () { triggerGPS(); });

    const video = document.getElementById('webcam-video');
    function startCamera() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } }, audio: false })
                .then(function (stream) { webcamStream = stream; video.srcObject = stream; })
                .catch(function () { Swal.fire({ icon: 'error', title: 'Kamera Tidak Tersedia', text: 'Pastikan izin kamera telah diaktifkan di browser Anda.' }); });
        }
    }
    function stopCamera() { if (webcamStream) { webcamStream.getTracks().forEach(function (t) { t.stop(); }); webcamStream = null; } }

    function openModal(tipe) {
        activeAttendanceType = tipe;
        capturedImage = null;
        $('#modal-title').text(tipe === 'masuk' ? 'Presensi Masuk' : 'Presensi Pulang');
        $('#preview-photo').addClass('hidden');
        $('#modal-snap-controls').removeClass('hidden');
        $('#modal-submit-controls').addClass('hidden');
        $('#face-guide').removeClass('hidden');
        $(video).removeClass('hidden');
        $('#modal-absen').removeClass('hidden').addClass('flex');
        startCamera();
    }
    function closeModal() { $('#modal-absen').addClass('hidden').removeClass('flex'); stopCamera(); }
    $('#btn-trigger-absen, #btn-trigger-pulang').click(function () { if ($(this).prop('disabled')) return; openModal($(this).data('tipe') || 'masuk'); });
    $('#btn-close-modal').click(closeModal);

    $('#btn-snap-modal').click(function () {
        const canvas = document.getElementById('snapshot-canvas');
        const size = Math.min(video.videoWidth || 480, video.videoHeight || 480);
        canvas.width = size; canvas.height = size;
        const ctx = canvas.getContext('2d');
        ctx.translate(size, 0); ctx.scale(-1, 1);
        const startX = ((video.videoWidth || 480) - size) / 2;
        const startY = ((video.videoHeight || 480) - size) / 2;
        ctx.drawImage(video, startX, startY, size, size, 0, 0, size, size);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        capturedImage = canvas.toDataURL('image/jpeg', 0.88);
        $('#preview-photo').attr('src', capturedImage).removeClass('hidden');
        $(video).addClass('hidden'); $('#face-guide').addClass('hidden');
        $('#modal-snap-controls').addClass('hidden');
        $('#modal-submit-controls').removeClass('hidden').css('display', 'flex');
    });
    $('#btn-retake-modal').click(function () {
        capturedImage = null;
        $('#preview-photo').addClass('hidden');
        $(video).removeClass('hidden'); $('#face-guide').removeClass('hidden');
        $('#modal-snap-controls').removeClass('hidden');
        $('#modal-submit-controls').addClass('hidden');
    });

    $('#btn-submit-modal').click(function () {
        if (!capturedImage) { Swal.fire({ icon: 'warning', title: 'Swafoto Wajib', text: 'Silakan ambil foto selfie wajah Anda terlebih dahulu!' }); return; }
        if (!isWithinRadius) { Swal.fire({ icon: 'error', title: 'Di Luar Radius', text: 'Posisi berada di luar radius kantor!' }); return; }
        Swal.fire({ title: 'Menyimpan Absensi...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        $.ajax({
            url: "{{ route('ajax.simpan-absen') }}",
            type: 'POST',
            data: { tipe: activeAttendanceType, image_data: capturedImage, latitude: currentLat, longitude: currentLng, _token: CSRF },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    closeModal();
                    Swal.fire({ icon:'success', title:'Absensi Berhasil!', text: res.message }).then(() => location.reload());
                } else Swal.fire({ icon:'error', title:'Gagal', text: res.message });
            },
            error: function () { Swal.fire({ icon:'error', title:'Error', text:'Terjadi kegagalan komunikasi dengan server.' }); }
        });
    });
});
</script>
@endpush

