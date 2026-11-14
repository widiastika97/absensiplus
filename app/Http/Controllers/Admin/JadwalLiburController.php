<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalLibur;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalLiburController extends Controller
{
    public function index(): View
    {
        $holidays = JadwalLibur::orderBy('tanggal_mulai')->get();
        $karyawanList = Karyawan::orderBy('nama')->get(['id', 'nip', 'nama', 'departemen', 'agama']);

        return view('admin.jadwal-libur', [
            'holidays' => $holidays,
            'karyawanList' => $karyawanList,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        JadwalLibur::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Jadwal libur berhasil ditambahkan.']);
        }

        return back()->with('success', 'Jadwal libur berhasil ditambahkan.');
    }

    public function update(Request $request, JadwalLibur $jadwalLibur)
    {
        $data = $this->validated($request);

        $jadwalLibur->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Jadwal libur berhasil diperbarui.']);
        }

        return back()->with('success', 'Jadwal libur berhasil diperbarui.');
    }

    public function destroy(Request $request, JadwalLibur $jadwalLibur)
    {
        $jadwalLibur->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Jadwal libur berhasil dihapus.']);
        }

        return back()->with('success', 'Jadwal libur berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama_libur' => ['required', 'string', 'max:150'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'tipe_target' => ['required', 'in:semua,karyawan,agama'],
            'target_agama' => ['nullable', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya'],
            'target_karyawan_ids' => ['nullable', 'array'],
            'target_karyawan_ids.*' => ['integer'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['tanggal_selesai'] = $data['tanggal_selesai'] ?? $data['tanggal_mulai'];
        if ($data['tanggal_selesai'] < $data['tanggal_mulai']) {
            $data['tanggal_selesai'] = $data['tanggal_mulai'];
        }

        $data['target_agama'] = $data['target_agama'] ?? null;
        $data['target_karyawan_ids'] = ! empty($data['target_karyawan_ids'])
            ? array_map('intval', $data['target_karyawan_ids'])
            : null;
        $data['deskripsi'] = $data['deskripsi'] ?? null;

        return $data;
    }
}
