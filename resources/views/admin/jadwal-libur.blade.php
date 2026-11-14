@extends('layouts.admin')

@section('title', 'Master Jadwal Libur')

@section('content')
@include('layouts.admin-header')

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-5 space-y-5">
    @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h1 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-amber-600"></i> Master Jadwal Libur & Cuti Bersama
            </h1>
            <button onclick="openModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold cursor-pointer">
                <i class="fa-solid fa-plus"></i> Tambah Jadwal Libur
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="p-3">Nama Libur</th>
                        <th class="p-3">Periode</th>
                        <th class="p-3">Target</th>
                        <th class="p-3">Deskripsi</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($holidays as $libur)
                    <tr class="hover:bg-slate-50/60">
                        <td class="p-3 font-bold text-slate-900">{{ $libur->nama_libur }}</td>
                        <td class="p-3">{{ $libur->tanggal_mulai->translatedFormat('d M Y') }} — {{ $libur->tanggal_selesai->translatedFormat('d M Y') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700">{{ strtoupper($libur->tipe_target) }}</span>
                            @if($libur->tipe_target === 'agama') <span class="text-[10px] ml-1">{{ $libur->target_agama }}</span> @endif
                        </td>
                        <td class="p-3 text-slate-500 max-w-xs truncate">{{ $libur->deskripsi }}</td>
                        <td class="p-3">
                            <div class="flex gap-1.5">
                                <button onclick='editLibur(@json($libur->toArray()))' class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg cursor-pointer"><i class="fa-solid fa-pen"></i></button>
                                <button onclick="hapusLibur({{ $libur->id }})" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg cursor-pointer"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-400 italic">Belum ada jadwal libur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal -->
<div id="modal-libur" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white rounded-3xl p-5 space-y-3 max-h-[90vh] overflow-y-auto">
        <h3 id="modal-title" class="text-sm font-bold text-slate-900">Tambah Jadwal Libur</h3>
        <form id="form-libur" class="space-y-3">
            @csrf
            <input type="hidden" name="id" id="libur-id">
            <input type="text" name="nama_libur" id="libur-nama" placeholder="Nama hari libur" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[10px] font-bold text-slate-500">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="libur-mulai" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="libur-selesai" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[10px] font-bold text-slate-500">Tipe Target</label>
                    <select name="tipe_target" id="libur-tipe" onchange="toggleTarget()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <option value="semua">Semua Karyawan</option>
                        <option value="agama">Per Agama</option>
                        <option value="karyawan">Karyawan Tertentu</option>
                    </select>
                </div>
                <div id="wrap-agama" class="hidden">
                    <label class="text-[10px] font-bold text-slate-500">Agama</label>
                    <select name="target_agama" id="libur-agama" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $agama)
                            <option value="{{ $agama }}">{{ $agama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="wrap-karyawan" class="hidden">
                <label class="text-[10px] font-bold text-slate-500">Karyawan (multi pilih)</label>
                <select name="target_karyawan_ids[]" id="libur-karyawan" multiple size="4" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    @foreach($karyawanList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }} — {{ $k->departemen }}</option>
                    @endforeach
                </select>
            </div>
            <textarea name="deskripsi" id="libur-deskripsi" rows="2" placeholder="Deskripsi (opsional)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
            <div class="flex gap-2">
                <button type="button" onclick="closeModal()" class="flex-1 py-2.5 bg-slate-100 rounded-xl text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function toggleTarget() {
    const tipe = $('#libur-tipe').val();
    $('#wrap-agama').toggleClass('hidden', tipe !== 'agama');
    $('#wrap-karyawan').toggleClass('hidden', tipe !== 'karyawan');
}

function openModal() {
    $('#modal-title').text('Tambah Jadwal Libur');
    $('#form-libur')[0].reset();
    $('#libur-id').val('');
    toggleTarget();
    $('#modal-libur').removeClass('hidden').addClass('flex');
}
function closeModal() { $('#modal-libur').addClass('hidden').removeClass('flex'); }

function editLibur(l) {
    $('#modal-title').text('Edit: ' + l.nama_libur);
    $('#libur-id').val(l.id); $('#libur-nama').val(l.nama_libur);
    $('#libur-mulai').val(l.tanggal_mulai); $('#libur-selesai').val(l.tanggal_selesai);
    $('#libur-tipe').val(l.tipe_target); $('#libur-agama').val(l.target_agama || 'Islam');
    $('#libur-deskripsi').val(l.deskripsi || '');
    $('#libur-karyawan').val(l.target_karyawan_ids || []).trigger('change');
    toggleTarget();
    $('#modal-libur').removeClass('hidden').addClass('flex');
}

$('#form-libur').on('submit', function (e) {
    e.preventDefault();
    const id = $('#libur-id').val();
    const fd = new FormData(this);
    const url = id ? "{{ url('admin/jadwal-libur') }}/" + id : "{{ route('admin.jadwal-libur.store') }}";

    $.ajax({
        url, type: id ? 'POST' : 'POST',
        data: fd,
        processData: false, contentType: false,
        headers: id ? { 'X-HTTP-Method-Override': 'PUT' } : {},
        success: (res) => Swal.fire({icon:'success', text:res.message, timer:1500, showConfirmButton:false}).then(() => location.reload()),
        error: (xhr) => Swal.fire({icon:'error', text: xhr.responseJSON?.message || 'Gagal menyimpan.'})
    });
});

function hapusLibur(id) {
    Swal.fire({icon:'warning', title:'Hapus jadwal libur ini?', showCancelButton:true}).then(r => {
        if (r.isConfirmed) {
            $.ajax({
                url: "{{ url('admin/jadwal-libur') }}/" + id,
                type: 'DELETE',
                data: { _token: CSRF },
                success: () => Swal.fire({icon:'success', text:'Jadwal libur berhasil dihapus.', timer:1500, showConfirmButton:false}).then(() => location.reload()),
                error: () => Swal.fire({icon:'error', text:'Gagal menghapus.'})
            });
        }
    });
}
</script>
@endpush
