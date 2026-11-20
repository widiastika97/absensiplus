<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectLoopTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_gets_no_loop_on_root(): void
    {
        $role = Role::create(['kode_role' => 'ROLE_ADMIN', 'nama_role' => 'Admin', 'izin_akses' => [], 'is_system' => true]);
        $user = Karyawan::factory()->create(['role_id' => $role->id, 'is_active' => 1]);

        // langkah 1: authenticated user yang membuka '/' TIDAK boleh di-redirect ke '/' sendiri (loop)
        $r = $this->actingAs($user, 'karyawan')->get('/');
        $loc = $r->headers->get('Location', '');
        $this->assertNotSame(url('/'), $loc, 'REDIRECT LOOP: Location masih /');

        // langkah 2: apapun targetnya, harus berhenti (bukan / lagi)
        if ($loc !== '') {
            $path = parse_url($loc, PHP_URL_PATH) ?: '/';
            $r2 = $this->actingAs($user, 'karyawan')->get($path);
            $this->assertNotSame(url('/'), $r2->headers->get('Location', '/'), 'loop kedua');
        } else {
            $this->assertSame(200, $r->status());
        }
    }
}
