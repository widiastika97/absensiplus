@extends('layouts.app')

@section('title', 'Pengajuan Cuti & Izin')

@section('content')
@include('layouts.karyawan-header')

<main class="max-w-6xl mx-auto px-2.5 sm:px-4 py-4 sm:py-6 pb-28 sm:pb-6 space-y-4 sm:space-y-6">
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h1 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <i class="fa-solid fa-envelope-open-text text-blue-600"></i> Pengajuan Cuti & Izin
        </h1>

        <div class="grid grid-cols-3 gap-3 mt-4">
            <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-xl text-center">
                <p class="text-lg font-bold text-indigo-700">{{ $kuota['total_quota'] }}</p>
                <p class="text-[10px] text-indigo-500 font-semibold">JATAH TAHUNAN</p>
            </div>
            <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl text-center">
                <p class="text-lg font-bold text-amber-700">{{ $kuota['used_quota'] + $kuota['pending_quota'] }}</p>
                <p class="text-[10px] text-amber-500 font-semibold">TERPAKAI + PENDING</p>
            </div>
            <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-center">
                <p class="text-lg font-bold text-emerald-700">{{ $kuota['remaining_quota'] }}</p>
                <p class="text-[10px] text-emerald-500 font-semibold">SISA KUOTA</p>
            </div>
        </div>
    </div>

    <!-- Form Pengajuan -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xs font-bold text-slate-900 mb-4">Form Pengajuan Baru</h2>

        <form id="form-cuti" class="space-y-3" enctype="multipart/form-data">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-[11px] font-bold text-slate-600">Jenis Cuti / Izin</label>
                    <select name="tipe_cuti" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <option value="tahunan">Cuti Tahunan (potong kuota)</option>
                        <option value="sakit">Cuti Sakit (Surat Dokter)</option>
                        <option value="melahirkan">Cuti Melahirkan (Maternity)</option>
                        <option value="izin">Izin Urusan Pribadi / Keluarga</option>
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
                    <label class="text-[11px] font-bold text-slate-600">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-600">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Alasan / Keterangan</label>
                <textarea name="alasan" rows="3" required class="w-full mt-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs" placeholder="Jelaskan alasan pengajuan cuti Anda..."></textarea>
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600">Lampiran Bukti (wajib untuk cuti khusus) — maks 5 MB</label>
                <input type="file" name="lampiran_dokumen" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="w-full mt-1 text-xs">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm">
                <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan
            </button>
        </form>
    </div>

    <!-- Riwayat Pengajuan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h2 class="text-xs font-bold text-slate-900">Riwayat Pengajuan Cuti</h2>
        </div>
        @forelse($riwayatCuti as $cuti)
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <p class="text-xs font-bold text-slate-900">{{ $cuti->label_tipe }}</p>
                    <p class="text-[10px] text-slate-500">{{ $cuti->tanggal_mulai->translatedFormat('d M Y') }} s/d {{ $cuti->tanggal_selesai->translatedFormat('d M Y') }} &bull; {{ $cuti->total_hari }} hari &bull; Approver: {{ $cuti->target_approver_name }}</p>
                    @if($cuti->alasan_penolakan)
                        <p class="text-[10px] text-rose-500 mt-0.5"><i class="fa-solid fa-comment"></i> {{ $cuti->alasan_penolakan }}</p>
                    @endif
                </div>
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg {{ $cuti->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($cuti->status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                    {{ strtoupper($cuti->status) }}
                </span>
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 text-xs">Belum ada pengajuan cuti.</div>
        @endforelse
    </div>
</main>

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    $('#form-cuti').on('submit', function (e) {
        e.preventDefault();
        const form = this;
        const fd = new FormData(form);
        fd.append('_token', CSRF);

        Swal.fire({ title: 'Mengirim...', didOpen: () => Swal.showLoading() });

        $.ajax({
            url: "{{ route('ajax.submit-cuti') }}",
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
