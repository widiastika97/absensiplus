<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use Illuminate\Http\Request;

class CrudLokasiController extends Controller
{
    public function __invoke(Request $request)
    {
        $action = $request->input('action');

        return match ($action) {
            'tambah' => $this->tambah($request),
            'edit' => $this->edit($request),
            'hapus' => $this->hapus($request),
            'toggle_active' => $this->toggleActive($request),
            default => response()->json(['status' => 'error', 'message' => 'Aksi tidak dikenali']),
        };
    }

    private function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'kota' => ['nullable', 'string', 'max:100'],
            'lat' => ['required', 'numeric', 'not_in:0'],
            'lng' => ['required', 'numeric', 'not_in:0'],
            'radius' => ['required', 'integer', 'min:10', 'max:10000'],
        ];
    }

    private function messages(): array
    {
        return [
            'kode.required' => 'Kode kantor, nama lokasi, dan koordinat GPS wajib diisi',
            'nama.required' => 'Kode kantor, nama lokasi, dan koordinat GPS wajib diisi',
            'lat.not_in' => 'Kode kantor, nama lokasi, dan koordinat GPS wajib diisi',
            'lng.not_in' => 'Kode kantor, nama lokasi, dan koordinat GPS wajib diisi',
        ];
    }

    private function tambah(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());

        if (LokasiKantor::where('kode_lokasi', $data['kode'])->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Kode lokasi sudah digunakan oleh kantor lain'], 422);
        }

        LokasiKantor::create([
            'kode_lokasi' => $data['kode'],
            'nama_lokasi' => $data['nama'],
            'alamat' => $data['alamat'] ?? '',
            'kota' => $data['kota'] ?? '',
            'latitude' => $data['lat'],
            'longitude' => $data['lng'],
            'radius' => $data['radius'],
            'is_active' => true,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Titik lokasi kantor baru berhasil ditambahkan']);
    }

    private function edit(Request $request)
    {
        $data = $request->validate($this->rules() + ['id' => ['required', 'integer', 'min:1']], $this->messages());

        $lokasi = LokasiKantor::findOrFail($data['id']);

        $duplicate = LokasiKantor::where('kode_lokasi', $data['kode'])->where('id', '!=', $data['id'])->exists();
        if ($duplicate) {
            return response()->json(['status' => 'error', 'message' => 'Kode lokasi sudah digunakan oleh kantor lain'], 422);
        }

        $lokasi->update([
            'kode_lokasi' => $data['kode'],
            'nama_lokasi' => $data['nama'],
            'alamat' => $data['alamat'] ?? $lokasi->alamat,
            'kota' => $data['kota'] ?? $lokasi->kota,
            'latitude' => $data['lat'],
            'longitude' => $data['lng'],
            'radius' => $data['radius'],
            'is_active' => (bool) $request->input('is_active', true),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data titik lokasi kantor berhasil diperbarui']);
    }

    private function hapus(Request $request)
    {
        $data = $request->validate(['id' => ['required', 'integer', 'min:1']]);

        if (LokasiKantor::count() <= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sistem wajib memiliki minimal satu titik lokasi kantor aktif',
            ], 422);
        }

        $jumlahPresensi = LokasiKantor::findOrFail($data['id'])->presensi()->count();

        if ($jumlahPresensi > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Titik lokasi ini memiliki {$jumlahPresensi} data riwayat presensi karyawan. Demi keutuhan data audit, lokasi tidak dapat dihapus permanen. Anda dapat menonaktifkan status kantor ini.",
            ], 422);
        }

        LokasiKantor::where('id', $data['id'])->delete();

        return response()->json(['status' => 'success', 'message' => 'Titik lokasi kantor berhasil dihapus']);
    }

    private function toggleActive(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'boolean'],
        ]);

        if (! $data['status'] && LokasiKantor::where('is_active', 1)->count() <= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Setidaknya harus ada satu lokasi kantor yang aktif',
            ], 422);
        }

        LokasiKantor::where('id', $data['id'])->update(['is_active' => $data['status']]);

        return response()->json(['status' => 'success', 'message' => 'Status aktif lokasi kantor diperbarui']);
    }
}
