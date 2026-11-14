<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class CrudRoleController extends Controller
{
    public function __invoke(Request $request)
    {
        $action = $request->input('action');

        return match ($action) {
            'tambah' => $this->tambah($request),
            'edit' => $this->edit($request),
            'hapus' => $this->hapus($request),
            default => response()->json(['status' => 'error', 'message' => 'Aksi tidak dikenali']),
        };
    }

    private function tambah(Request $request)
    {
        $data = $request->validate([
            'kode_role' => ['required', 'string', 'max:50'],
            'nama_role' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'izin_akses' => ['nullable', 'json'],
        ]);

        $kode = strtoupper($data['kode_role']);

        if ($kode === 'ROLE_SUPER_ADMIN') {
            return response()->json(['status' => 'error', 'message' => 'Kode ROLE_SUPER_ADMIN tidak dapat digunakan'], 422);
        }

        if (Role::where('kode_role', $kode)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Kode role sudah digunakan oleh role lain'], 422);
        }

        Role::create([
            'kode_role' => $kode,
            'nama_role' => $data['nama_role'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'izin_akses' => json_decode($data['izin_akses'] ?? '[]', true),
            'is_system' => false,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Role baru berhasil ditambahkan']);
    }

    private function edit(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'nama_role' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'izin_akses' => ['nullable', 'json'],
        ]);

        $role = Role::findOrFail($data['id']);

        if ($role->kode_role === 'ROLE_SUPER_ADMIN') {
            return response()->json(['status' => 'error', 'message' => 'Role Super Administrator tidak dapat diubah'], 422);
        }

        $role->update([
            'nama_role' => $data['nama_role'],
            'deskripsi' => $data['deskripsi'] ?? $role->deskripsi,
            'izin_akses' => json_decode($data['izin_akses'] ?? '[]', true),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Hak akses role berhasil diperbarui']);
    }

    private function hapus(Request $request)
    {
        $data = $request->validate(['id' => ['required', 'integer', 'min:1']]);

        $role = Role::findOrFail($data['id']);

        if ($role->kode_role === 'ROLE_SUPER_ADMIN') {
            return response()->json(['status' => 'error', 'message' => 'Role Super Administrator tidak dapat dihapus'], 422);
        }

        $userCount = $role->karyawan()->count();

        if ($userCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role ini masih digunakan oleh '.$userCount.' karyawan. Pindahkan user ke role lain terlebih dahulu.',
            ], 422);
        }

        $role->delete();

        return response()->json(['status' => 'success', 'message' => 'Role berhasil dihapus']);
    }
}
