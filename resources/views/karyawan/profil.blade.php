@extends('layouts.app')

@section('title', 'Profil Karyawan')

@section('content')
@include('layouts.karyawan-header')

<main class="max-w-6xl mx-auto px-2.5 sm:px-4 py-4 sm:py-6 pb-28 sm:pb-6 space-y-4 sm:space-y-6">
    <!-- Kartu Profil -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center gap-5">
        <div class="relative">
            @php
                $fotoUrl = auth('karyawan')->user()->foto_profil && auth('karyawan')->user()->foto_profil !== 'default-avatar.png'
                    ? asset('storage/avatars/' . auth('karyawan')->user()->foto_profil)
                    : 'https://ui-avatars.com/api/?name=' . urlencode(auth('karyawan')->user()->nama) . '&background=0284c7&color=fff&size=128&bold=true';
            @endphp
            <img id="img-foto-profil" src="{{ $fotoUrl }}" class="w-24 h-24 rounded-2xl object-cover border-4 border-blue-100 shadow-md">
            <label for="input-foto-profil" class="absolute -bottom-1 -right-1 w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-xl flex items-center justify-center cursor-pointer shadow-md" title="Ubah Foto">
                <i class="fa-solid fa-camera text-xs"></i>
            </label>
            <input type="file" id="input-foto-profil" accept="image/jpeg,image/png,image/webp" class="hidden">
        </div>
        <div class="text-center sm:text-left flex-1">
            <h1 class="text-lg font-bold text-slate-900">{{ $user->nama }}</h1>
            <p class="text-xs text-slate-500">{{ $user->nip }} &bull; {{ $user->jabatan }}</p>
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5 mt-2">
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">{{ $user->role->nama_role ?? 'Staff' }}</span>
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-slate-50 text-slate-600 border border-slate-100">{{ $lokasiKantor }}</span>
                @if($shiftHariIni)
                    <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $shiftHariIni->shift->nama_shift }}</span>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 text-center">
            <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl min-w-[100px]">
                <p class="text-lg font-bold text-slate-900">{{ $totalHadirBulanIni }}</p>
                <p class="text-[10px] text-slate-400 font-semibold">HADIR BULAN INI</p>
            </div>
            <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl min-w-[100px]">
                <p class="text-lg font-bold text-slate-900">{{ $kuota['remaining_quota'] }}/{{ $kuota['total_quota'] }}</p>
                <p class="text-[10px] text-slate-400 font-semibold">SISA CUTI</p>
            </div>
        </div>
    </div>

    <!-- Form Edit Profil -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xs font-bold text-slate-900 mb-3">Data Diri</h2>
        <form method="POST" action="{{ route('profil.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @csrf
            <div>
                <label class="text-[11px] font-bold text-slate-600">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ $user->nama }}" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">No. HP</label>
                <input type="text" name="no_hp" value="{{ $user->no_hp }}" class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Email</label>
                <input type="email" value="{{ $user->email }}" disabled class="w-full mt-1 px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-400">
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Departemen</label>
                <input type="text" value="{{ $user->departemen }}" disabled class="w-full mt-1 px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-400">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Pengajuan Lainnya -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h2 class="text-xs font-bold text-slate-900">Riwayat Pengajuan Dispensasi (Terlambat / Pulang Cepat)</h2>
        </div>
        @forelse($riwayatPengajuanLain as $pl)
            <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-slate-900">{{ $pl->label_tipe }}</p>
                    <p class="text-[10px] text-slate-500">{{ $pl->tanggal->translatedFormat('d M Y') }} &bull; Estimasi jam {{ substr($pl->jam_estimasi, 0, 5) }}</p>
                    @if($pl->alasan_penolakan)
                        <p class="text-[10px] text-rose-500">{{ $pl->alasan_penolakan }}</p>
                    @endif
                </div>
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg {{ $pl->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($pl->status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                    {{ strtoupper($pl->status) }}
                </span>
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 text-xs">Belum ada pengajuan dispensasi.</div>
        @endforelse
    </div>

    <!-- Form Pengajuan Dispensasi -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xs font-bold text-slate-900 mb-3">Ajukan Dispensasi (Terlambat / Pulang Lebih Awal)</h2>
        <form id="form-pengajuan-lain" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="text-[11px] font-bold text-slate-600">Jenis Pengajuan</label>
                <select name="tipe_pengajuan" class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <option value="terlambat">Terlambat Masuk Kerja</option>
                    <option value="pulang_cepat">Pulang Lebih Awal</option>
                </select>
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Approver / Atasan</label>
                <select name="target_approver_id" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <option value="">-- Pilih Atasan --</option>
                    @foreach($approverList as $app)
                        <option value="{{ $app->id }}">{{ $app->nama }} ({{ $app->jabatan }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Tanggal Kejadian</label>
                <input type="date" name="tanggal" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Estimasi Jam</label>
                <input type="time" name="jam_estimasi" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
            <div class="sm:col-span-2">
                <label class="text-[11px] font-bold text-slate-600">Alasan</label>
                <textarea name="alasan" rows="2" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="text-[11px] font-bold text-slate-600">Foto Bukti (wajib untuk Terlambat) — maks 5 MB</label>
                <input type="file" name="foto_bukti" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 text-xs">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</main>

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // Upload foto profil
    $('#input-foto-profil').on('change', function () {
        if (!this.files.length) return;
        const fd = new FormData();
        fd.append('foto_profil', this.files[0]);
        fd.append('_token', CSRF);

        $.ajax({
            url: "{{ route('ajax.update-foto-profil') }}",
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#img-foto-profil').attr('src', res.foto_url + '?t=' + Date.now());
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || (xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors)[0][0] : 'Gagal mengunggah.');
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
            }
        });
    });

    // Pengajuan dispensasi
    $('#form-pengajuan-lain').on('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('_token', CSRF);

        $.ajax({
            url: "{{ route('ajax.submit-pengajuan-lain') }}",
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Terkirim', text: res.message }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || (xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors)[0][0] : 'Terjadi kesalahan.');
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
            }
        });
    });
</script>
@endpush
@endsection
