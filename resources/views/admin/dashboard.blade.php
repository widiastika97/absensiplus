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

<div id="panel-region">
    @include('admin.partials.dashboard-panel')
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

</main>

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
            <div id="wrap-karyawan-password">
                <input type="password" name="password" id="karyawan-password" placeholder="Password awal (default: 123456)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>
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
            if (res.status === 'success') { Swal.fire({icon:'success', title:'Berhasil', text:res.message, timer:1600, showConfirmButton:false}).then(onSuccess || (()=>refreshRegion())); }
            else Swal.fire({icon:'error', title:'Gagal', text:res.message});
        },
        error: (xhr) => {
            const msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
            Swal.fire({icon:'error', title:'Gagal', text: msg});
        }
    });
}

// Ambil ulang panel dashboard via AJAX (tanpa reload halaman)
function refreshRegion(extra = {}) {
    const params = {
        dari: $('#tab-logs input[name="dari"]').val(),
        sampai: $('#tab-logs input[name="sampai"]').val(),
        dept: $('#tab-logs select[name="dept"]').val() || 'all',
        roster_bulan: $('#panel-region select[name="roster_bulan"]').val(),
        roster_tahun: $('#panel-region select[name="roster_tahun"]').val(),
    };
    const merged = Object.assign(params, extra);
    // simpan posisi scroll kalender roster agar tidak kembali ke awal setelah refresh
    const rosterScroll = { left: $('#roster-matrix-wrap').scrollLeft(), top: $('#roster-matrix-wrap').scrollTop() };
    return $.get("{{ route('admin.ajax.dashboard-data') }}", merged)
        .done((res) => {
            $('#panel-region').html(res.html);
            aktifkanTab(sessionStorage.getItem('dashboard.activeTab') || 'tab-logs');
            filterRosterKaryawan();
            $('#roster-matrix-wrap').scrollLeft(rosterScroll.left).scrollTop(rosterScroll.top);
            const q = $.param({ dari: merged.dari, sampai: merged.sampai, dept: merged.dept, roster_bulan: merged.roster_bulan, roster_tahun: merged.roster_tahun });
            history.replaceState(null, '', `{{ route('admin.dashboard') }}?${q}${location.hash || ''}`);
        })
        .fail(() => Swal.fire({icon:'error', title:'Gagal', text:'Gagal memuat data dashboard.'}));
}

// Navigasi bulan roster tanpa reload
function refreshRoster(bulan, tahun) {
    const b = bulan || $('#panel-region select[name="roster_bulan"]').val();
    const t = tahun || $('#panel-region select[name="roster_tahun"]').val();
    refreshRegion({ roster_bulan: b, roster_tahun: t });
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

// (binding didelegasikan karena #admin-tabs dirender ulang via AJAX)
$(document).off('click.adminTabs').on('click.adminTabs', '#admin-tabs button', function () {
    aktifkanTab($(this).data('tab'));
});

// Filter log absensi via AJAX (tanpa reload)
function resetFilterLogs() {
    refreshRegion({ dari: '', sampai: '', dept: 'all' });
}

$(document).on('submit', '#form-filter-logs', function (e) {
    e.preventDefault();
    refreshRegion({
        dari: $(this).find('input[name="dari"]').val(),
        sampai: $(this).find('input[name="sampai"]').val(),
        dept: $(this).find('select[name="dept"]').val(),
    });
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
    ajaxPost("{{ route('admin.ajax.crud-lokasi') }}", data, () => { closeModalLokasi(); $('#form-lokasi')[0].reset(); });
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
    $('#wrap-karyawan-password').removeClass('hidden');
    $('#modal-karyawan').removeClass('hidden').addClass('flex');
}
function closeModalKaryawan() { $('#modal-karyawan').addClass('hidden').removeClass('flex'); }

function editKaryawan(k) {
    $('#modal-karyawan-title').text('Edit: ' + k.nama);
    $('#karyawan-id').val(k.id); $('#karyawan-nama').val(k.nama); $('#karyawan-email').val(k.email);
    $('#karyawan-departemen').val(k.departemen); $('#karyawan-jabatan').val(k.jabatan);
    $('#karyawan-role').val(k.role_id); $('#karyawan-agama').val(k.agama);
    $('#karyawan-nip').val(k.nip).prop('disabled', true);
    // sembunyikan field password; jangan menyembunyikan container modal
    $('#wrap-karyawan-password').addClass('hidden');
    $('#modal-karyawan').removeClass('hidden').addClass('flex');
}

$('#form-karyawan').on('submit', function (e) {
    e.preventDefault();
    const id = $('#karyawan-id').val();
    const data = Object.fromEntries(new FormData(this).entries());
    data.action = id ? 'edit' : 'tambah';
    if (id) delete data.password;
    ajaxPost("{{ route('admin.ajax.crud-karyawan') }}", data, () => { closeModalKaryawan(); $('#form-karyawan')[0].reset(); });
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

// Catatan absensi admin (sistem eror / karyawan lupa absen)
function editCatatanAbsen(karyawanId, nama, tanggal, catatan) {
    Swal.fire({
        title: 'Catatan Absensi',
        input: 'textarea',
        inputLabel: nama + ' — ' + tanggal + ' (kosongkan untuk menghapus catatan)',
        inputValue: catatan || '',
        inputPlaceholder: 'cth: Sistem error / lupa absen, dihitung manual oleh admin.',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-save"></i> Simpan Catatan',
        confirmButtonColor: '#2563eb',
        cancelButtonText: 'Batal',
    }).then((r) => {
        if (r.isConfirmed) {
            ajaxPost("{{ route('admin.ajax.catatan-absensi') }}", { karyawan_id: karyawanId, tanggal: tanggal, catatan: r.value || '' });
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
                /* Select2 multi-select agar menyatu dengan tema modal */
                .select2-container--default .select2-selection--multiple { border-radius: 10px; border-color: #cbd5e1; min-height: 36px; font-size: 12px; }
                .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #6366f1; }
                .select2-container--default .select2-selection--multiple .select2-selection__choice { background: #eef2ff; border-color: #c7d2fe; color: #4338ca; font-weight: 600; font-size: 11px; }
                .select2-dropdown { border-radius: 10px; font-size: 12px; }
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
                        <option value="7_hari">7 Hari Kerja (Masuk Tiap Hari)</option>
                        <option value="6_1" selected>6 Hari Kerja - 1 Off</option>
                        <option value="5_2">5 Hari Kerja - 2 Off (Off Sabtu-Minggu)</option>
                        <option value="rotasi_pagi_siang">Rotasi Pagi/Siang per Minggu</option>
                        <option value="2p_2s_2m_2off">2 Pagi - 2 Siang - 2 Malam - 2 Off</option>
                    </select>
                </div>
                <div id="wrap-gen-hari-off">
                    <label class="gen-label">Hari Libur (pilih maks. <span id="gen-max-off">1</span> hari)</label>
                    <select id="gen-hari-off" multiple size="7" class="swal2-input gen-in">
                        @foreach(['Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6,'Minggu'=>7] as $nmHari => $noHari)
                        <option value="{{ $noHari }}" {{ $noHari === 7 ? 'selected' : '' }}>{{ $nmHari }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="gen-label">Bulan</label><select id="gen-bulan" class="swal2-input gen-in">@for($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ $i === now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}</option> @endfor</select></div>
                <div><label class="gen-label">Tahun</label><input id="gen-tahun" type="number" value="{{ now()->year }}" min="2024" max="2030" class="swal2-input gen-in"></div>
                <div><label class="gen-label">Shift Pagi/Kerja</label><select id="gen-shift-pagi" class="swal2-input gen-in">@foreach($shiftsListJS->where('is_off', 0) as $s) <option value="{{ $s->id }}">{{ $s->kode_shift }} • {{ $s->nama_shift }}</option> @endforeach</select></div>
            </div>
            <div class="gen-sec">
                <div>
                    <h4><i class="fa-solid fa-clock"></i> Jam Kerja</h4>
                    <label class="gen-label">Jam Masuk</label><input type="time" id="gen-jam-masuk" value="{{ $jamKerja['tetap']['masuk'] }}" class="swal2-input gen-in">
                    <label class="gen-label" style="margin-top:6px">Jam Pulang</label><input type="time" id="gen-jam-pulang" value="{{ $jamKerja['tetap']['pulang'] }}" class="swal2-input gen-in">
                </div>
                <p class="full text-[9px] text-slate-400">Jam ini diterapkan ke karyawan sesuai Jenis Karyawan yang dipilih saat generate. Karyawan dengan jam kerja custom diabaikan.</p>
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
            const pola = $('#gen-pola').val();
            return {
                karyawan_all: karyawan,
                jenis: $('#gen-jenis').val(),
                pola: pola,
                bulan: $('#gen-bulan').val(),
                tahun: $('#gen-tahun').val(),
                shift_pagi_id: $('#gen-shift-pagi').val(),
                hari_off: pola === '7_hari' ? [] : ($('#gen-hari-off').val() || []),
                karyawan_ids: karyawan === 'all' ? 'all' : [karyawan],
                jam_masuk: $('#gen-jam-masuk').val(),
                jam_pulang: $('#gen-jam-pulang').val(),
            };
        },
        didOpen: () => {
            // Batasan hari libur per pola: 0 = tanpa libur (7 hari kerja), dsb.
            const OFF_LIMIT = { '7_hari': 0, '6_1': 1, '5_2': 2, 'rotasi_pagi_siang': null, '2p_2s_2m_2off': null };
            const DEFAULT_OFF = { '6_1': ['7'], '5_2': ['6', '7'] };
            const $sel = $('#gen-hari-off');

            const initSelect2 = (maxOff) => {
                if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
                $sel.select2({
                    width: '100%',
                    dropdownParent: $('.swal2-popup'),
                    placeholder: 'Pilih hari libur',
                    closeOnSelect: false,
                    maximumSelectionLength: maxOff || 0,
                    language: {
                        maximumSelected: () => 'Maksimal ' + maxOff + ' hari libur untuk pola ini',
                    },
                });
            };

            const syncOffControls = () => {
                const pola = $('#gen-pola').val();
                const maxOff = OFF_LIMIT[pola];
                // Pola rotasi & 2p2s2m2off memakai aturan hari libur sendiri (scripted)
                const pakaiPilihanHari = maxOff !== null && maxOff !== undefined && maxOff > 0;
                $('#wrap-gen-hari-off').toggle(pakaiPilihanHari);
                $('#gen-max-off').text(maxOff || 0);
                if (pakaiPilihanHari) {
                    initSelect2(maxOff);
                    $sel.prop('disabled', false);
                    $sel.val(DEFAULT_OFF[pola] || []).trigger('change');
                } else {
                    // 7 hari kerja / pola scripted: matikan & kosongkan pilihan hari libur
                    if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
                    $sel.prop('disabled', true).val([]).trigger('change');
                }
            };
            $('#gen-pola').off('change.genPola').on('change.genPola', syncOffControls);
            syncOffControls();
        },
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const d = result.value;
            d._token = $('meta[name="csrf-token"]').attr('content');
            d.action = 'generate_pola_bulanan';
            $.post("{{ route('admin.ajax.crud-shift') }}", d, function (res) {
                if (res.status === 'success')
                    Swal.fire({icon:'success', title:'Selesai', text:res.message, timer:1500, showConfirmButton:false}).then(() => refreshRegion());
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
    ajaxPost("{{ route('admin.ajax.crud-shift') }}", data, () => { tutupModalShift(); $('#form-shift')[0].reset(); });
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

// Delegasi: elemen pencarian roster dirender ulang saat refreshRegion()
$(document).on('input', '#searchRosterKaryawan', filterRosterKaryawan);
$(document).on('change', '#filterDeptRoster', filterRosterKaryawan);

function openModalAssign(empId, tanggal) {
    const karyawanOpts = [];
    @foreach($karyawanList as $k) karyawanOpts.push({ id: {{ $k->id }}, t: '{{ $k->nama }} ({{ $k->nip }})' }); @endforeach
    const shiftOpts = [];
    @foreach($shiftsListJS as $s) shiftOpts.push({ id: {{ $s->id }}, t: '{{ $s->kode_shift }} • {{ $s->nama_shift }}' }); @endforeach

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
    ajaxPost("{{ route('admin.ajax.crud-role') }}", data, () => { tutupModalRole(); $('#form-role')[0].reset(); });
});
</script>
@endpush
