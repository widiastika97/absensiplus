<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\PrestasiPelanggaran;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrestasiPelanggaranTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_jam_kerja_default_sesuai_role_guru(): void
    {
        /** @var Karyawan $tetap */
        $tetap = Karyawan::factory()->create(['role_id' => 5]);
        $this->assertSame(['07:30', '14:30'], $tetap->jamKerjaGuru());

        /** @var Karyawan $kontrak */
        $kontrak = Karyawan::factory()->create(['role_id' => 6]);
        $this->assertSame(['07:15', '15:00'], $kontrak->jamKerjaGuru());

        /** @var Karyawan $honor */
        $honor = Karyawan::factory()->create(['role_id' => 7]);
        $this->assertNull($honor->jamKerjaGuru());

        /** @var Karyawan $staff */
        $staff = Karyawan::factory()->create(['role_id' => 4]);
        $this->assertNull($staff->kodeRoleGuru());
    }

    public function test_guru_biasa_tidak_dapat_mencatat_subjek_guru_hanya_admin(): void
    {
        Storage::fake('public');
        /** @var Karyawan $guru */
        $guru = Karyawan::factory()->create(['role_id' => 5]);
        /** @var Karyawan $admin */
        $admin = Karyawan::factory()->create(['role_id' => 1]);
        $siswa = Siswa::create(['nis' => 'NIS-1', 'nama' => 'Siswa Uji']);

        // Guru mencatat subjek guru -> ditolak walau sudah absen
        $this->absenMasuk($guru);
        $this->actingAs($guru, 'karyawan')
            ->postJson(route('ajax.submit-prestasi-pelanggaran'), $this->payload([
                'subjek' => 'guru',
                'guru_id' => $admin->id,
                'judul' => 'Juara',
                'kategori' => 'prestasi',
            ]))->assertStatus(403);

        // Guru belum absen -> ditolak
        /** @var Karyawan $guruTanpaAbsen */
        $guruTanpaAbsen = Karyawan::factory()->create(['role_id' => 5]);
        $this->actingAs($guruTanpaAbsen, 'karyawan')
            ->postJson(route('ajax.submit-prestasi-pelanggaran'), $this->payload([
                'kategori' => 'prestasi',
                'subjek' => 'siswa',
                'siswa_id' => $siswa->id,
                'judul' => 'Belum Absen',
            ]))->assertStatus(403);

        // Setelah presensi masuk -> diterima
        $this->actingAs($guru, 'karyawan')
            ->postJson(route('ajax.submit-prestasi-pelanggaran'), $this->payload([
                'kategori' => 'prestasi',
                'subjek' => 'siswa',
                'siswa_id' => $siswa->id,
                'judul' => 'Juara 1 Olimpiade',
            ]))->assertStatus(200);

        $this->assertTrue(Storage::disk('public')->exists(PrestasiPelanggaran::first()->foto_bukti));
        $this->assertSame(1, PrestasiPelanggaran::where('subjek', 'siswa')->count());

        // Admin lepas dari kewajiban absen
        $this->actingAs($admin, 'karyawan')
            ->postJson(route('ajax.submit-prestasi-pelanggaran'), $this->payload([
                'kategori' => 'pelanggaran',
                'subjek' => 'guru',
                'guru_id' => $guru->id,
                'judul' => 'Terlambat mengajar',
            ]))->assertStatus(200);

        $this->assertSame(1, PrestasiPelanggaran::where('subjek', 'guru')->count());
    }

    public function test_tanpa_foto_bukti_ditolak(): void
    {
        Storage::fake('public');
        /** @var Karyawan $admin */
        $admin = Karyawan::factory()->create(['role_id' => 1]);
        $siswa = Siswa::create(['nis' => 'NIS-3', 'nama' => 'Siswa Uji Tiga']);

        $this->actingAs($admin, 'karyawan')
            ->postJson(route('ajax.submit-prestasi-pelanggaran'), [
                'kategori' => 'prestasi',
                'subjek' => 'siswa',
                'siswa_id' => $siswa->id,
                'judul' => 'Tanpa Foto',
                'tanggal' => now()->toDateString(),
            ])->assertStatus(422);
    }

    private function payload(array $overrides): array
    {
        return array_merge([
            'kategori' => 'prestasi',
            'subjek' => 'siswa',
            'judul' => 'Judul',
            'tanggal' => now()->toDateString(),
            'foto_bukti' => UploadedFile::fake()->image('bukti.jpg'),
        ], $overrides);
    }

    private function absenMasuk(Karyawan $karyawan): void
    {
        $karyawan->presensi()->create([
            'lokasi_id' => 1,
            'tanggal' => now()->toDateString(),
            'tipe' => 'masuk',
            'jam' => '08:00:00',
            'foto_selfie' => 'selfie.jpg',
            'latitude' => 0,
            'longitude' => 0,
            'jarak_meter' => 10,
            'radius_diizinkan' => 100,
        ]);
    }

    public function test_halaman_prestasi_menyembunyikan_subjek_guru_dari_non_admin(): void
    {
        /** @var Karyawan $guru */
        $guru = Karyawan::factory()->create(['role_id' => 5]);
        /** @var Karyawan $admin */
        $admin = Karyawan::factory()->create(['role_id' => 1]);
        $siswa = Siswa::create(['nis' => 'NIS-2', 'nama' => 'Siswa Uji Dua']);

        PrestasiPelanggaran::create(['kategori' => 'prestasi', 'subjek' => 'siswa', 'siswa_id' => $siswa->id, 'judul' => 'Juara Fisika', 'tanggal' => now(), 'pelapor_id' => $admin->id]);
        PrestasiPelanggaran::create(['kategori' => 'prestasi', 'subjek' => 'guru', 'guru_id' => $guru->id, 'judul' => 'Riset Terbaik', 'tanggal' => now(), 'pelapor_id' => $admin->id]);

        $this->actingAs($guru, 'karyawan')
            ->get(route('prestasi'))
            ->assertOk()
            ->assertSee('Juara Fisika')
            ->assertDontSee('Riset Terbaik');

        $this->actingAs($admin, 'karyawan')
            ->get(route('prestasi'))
            ->assertOk()
            ->assertSee('Riset Terbaik');
    }

    public function test_laporan_prestasi_hanya_untuk_admin(): void
    {
        /** @var Karyawan $guru */
        $guru = Karyawan::factory()->create(['role_id' => 5]);
        /** @var Karyawan $admin */
        $admin = Karyawan::factory()->create(['role_id' => 1]);

        // Guru non-admin ditolak middleware permission (403)
        $this->actingAs($guru, 'karyawan')->get(route('laporan-prestasi'))->assertStatus(403);
        $this->actingAs($admin, 'karyawan')->get(route('laporan-prestasi'))->assertOk();
    }
}
