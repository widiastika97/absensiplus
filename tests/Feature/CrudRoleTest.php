<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudRoleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): Karyawan
    {
        /** @var Karyawan $admin */
        $admin = Karyawan::factory()->create(['role_id' => 1]);

        return $admin;
    }

    public function test_super_admin_role_tidak_bisa_diedit(): void
    {
        $this->actingAs($this->admin(), 'karyawan')
            ->postJson(route('admin.ajax.crud-role'), [
                'action' => 'edit',
                'id' => 1,
                'nama_role' => 'Diubah',
                'izin_akses' => '[]',
            ])->assertStatus(422);
    }

    public function test_super_admin_role_tidak_bisa_dihapus(): void
    {
        $this->actingAs($this->admin(), 'karyawan')
            ->postJson(route('admin.ajax.crud-role'), ['action' => 'hapus', 'id' => 1])
            ->assertStatus(422);

        $this->assertTrue(Role::where('kode_role', 'ROLE_SUPER_ADMIN')->exists());
    }

    public function test_kode_super_admin_tidak_bisa_dipakai_role_baru(): void
    {
        $this->actingAs($this->admin(), 'karyawan')
            ->postJson(route('admin.ajax.crud-role'), [
                'action' => 'tambah',
                'kode_role' => 'ROLE_SUPER_ADMIN',
                'nama_role' => 'Duplikat',
            ])->assertStatus(422);
    }

    public function test_role_sistem_bisa_dihapus_jika_tanpa_user(): void
    {
        // Semua role selain SUPER_ADMIN dapat dihapus meski is_system, selama tak ada user
        $roleTanpaUser = Role::where('kode_role', 'ROLE_GURU_TETAP')->first();

        $this->actingAs($this->admin(), 'karyawan')
            ->postJson(route('admin.ajax.crud-role'), ['action' => 'hapus', 'id' => $roleTanpaUser->id])
            ->assertStatus(200);

        $this->assertNull(Role::find($roleTanpaUser->id));
    }

    public function test_role_bertanda_user_tidak_bisa_dihapus(): void
    {
        /** @var Karyawan $guru */
        $guru = Karyawan::factory()->create(['role_id' => 4]);

        $this->actingAs($this->admin(), 'karyawan')
            ->postJson(route('admin.ajax.crud-role'), ['action' => 'hapus', 'id' => $guru->role_id])
            ->assertStatus(422);

        $this->assertTrue(Role::where('id', $guru->role_id)->exists());
    }

    public function test_role_biasa_bisa_ditambah_diedit_dihapus(): void
    {
        $this->actingAs($this->admin(), 'karyawan')
            ->postJson(route('admin.ajax.crud-role'), [
                'action' => 'tambah',
                'kode_role' => 'ROLE_KEPALA_SEKOLAH',
                'nama_role' => 'Kepala Sekolah',
                'izin_akses' => json_encode(['presensi_masuk_pulang', 'laporan_prestasi_pelanggaran']),
            ])->assertStatus(200);

        $role = Role::where('kode_role', 'ROLE_KEPALA_SEKOLAH')->first();
        $this->assertNotNull($role);

        $this->actingAs($this->admin(), 'karyawan')
            ->postJson(route('admin.ajax.crud-role'), [
                'action' => 'edit',
                'id' => $role->id,
                'nama_role' => 'Kepala Sekolah II',
                'izin_akses' => json_encode(['admin_rekap_ekspor']),
            ])->assertStatus(200);

        $this->assertSame('Kepala Sekolah II', $role->fresh()->nama_role);

        $this->actingAs($this->admin(), 'karyawan')
            ->postJson(route('admin.ajax.crud-role'), ['action' => 'hapus', 'id' => $role->id])
            ->assertStatus(200);

        $this->assertNull(Role::find($role->id));
    }
}
