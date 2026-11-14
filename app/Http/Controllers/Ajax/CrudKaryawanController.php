<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Pengaturan;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CrudKaryawanController extends Controller
{
    public function __invoke(Request $request)
    {
        $action = $request->input('action');

        return match ($action) {
            'tambah' => $this->tambah($request),
            'edit' => $this->edit($request),
            'reset_password' => $this->resetPassword($request),
            'hapus' => $this->hapus($request),
            'toggle_status' => $this->toggleStatus($request),
            default => response()->json(['status' => 'error', 'message' => 'Aksi tidak valid']),
        };
    }

    private function tambah(Request $request)
    {
        $data = $request->validate([
            'nip' => ['required', 'string', 'max:50', 'unique:karyawan,nip'],
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', 'unique:karyawan,email'],
            'departemen' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya'],
            'jam_masuk' => ['nullable'],
            'jam_pulang' => ['nullable'],
            'password' => ['nullable', 'string', 'min:5', 'max:100'],
        ], [
            'password.min' => 'Kata sandi minimal 5 karakter',
        ]);

        Karyawan::create([
            'nip' => $data['nip'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? '123456'),
            'departemen' => $data['departemen'],
            'jabatan' => $data['jabatan'],
            'role_id' => $data['role_id'],
            'agama' => $data['agama'],
            'jam_masuk' => $data['jam_masuk'] ?? $this->defaultJamKerja($data['role_id'])[0],
            'jam_pulang' => $data['jam_pulang'] ?? $this->defaultJamKerja($data['role_id'])[1],
            'is_active' => true,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Karyawan baru berhasil didaftarkan']);
    }

    /**
     * Default jam kerja: baca dari tabel pengaturan (bisa diubah admin via Manajemen Shift),
     * fallback ke mapping RoleSeeder; non-guru 08:00-17:00.
     */
    private function defaultJamKerja(int $roleId): array
    {
        $role = Role::find($roleId);
        $jam = $role ? Pengaturan::jamKerjaGuru($role->kode_role) : null;

        return $jam ?? ['08:00:00', '17:00:00'];
    }

    private function edit(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100'],
            'departemen' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya'],
            'jam_masuk' => ['nullable'],
            'jam_pulang' => ['nullable'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $karyawan = Karyawan::findOrFail($data['id']);

        $karyawan->update([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'departemen' => $data['departemen'],
            'jabatan' => $data['jabatan'],
            'role_id' => $data['role_id'],
            'agama' => $data['agama'],
            'jam_masuk' => $data['jam_masuk'] ?? $this->defaultJamKerja($data['role_id'])[0],
            'jam_pulang' => $data['jam_pulang'] ?? $this->defaultJamKerja($data['role_id'])[1],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data karyawan berhasil diperbarui']);
    }

    /**
     * Default jam kerja berdasarkan role guru; fallback 08:00-17:00 untuk non-guru.
     * Guru Honor (custom) tetap dipetakan ke nilai default sampai diubah admin.
     */
    private function resetPassword(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'password' => ['required', 'string', 'min:5'],
        ], [
            'password.min' => 'Kata sandi minimal 5 karakter',
        ]);

        Karyawan::where('id', $data['id'])->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Kata sandi berhasil direset']);
    }

    private function hapus(Request $request)
    {
        $data = $request->validate(['id' => ['required', 'integer', 'min:1']]);

        $karyawan = Karyawan::findOrFail($data['id']);
        $karyawan->presensi()->delete();
        $karyawan->delete();

        return response()->json(['status' => 'success', 'message' => 'Data karyawan dan riwayat berhasil dihapus']);
    }

    private function toggleStatus(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'boolean'],
        ]);

        Karyawan::where('id', $data['id'])->update(['is_active' => $data['status']]);

        return response()->json(['status' => 'success', 'message' => 'Status aktif karyawan diperbarui']);
    }
}
