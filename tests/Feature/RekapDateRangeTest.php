<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapDateRangeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_rekap_and_export_use_the_selected_inclusive_date_range(): void
    {
        $this->actingAs(Karyawan::findOrFail(1), 'karyawan');

        $inside = Karyawan::factory()->create(['nip' => 'IN-RANGE', 'nama' => 'In Range']);
        $outside = Karyawan::factory()->create(['nip' => 'OUT-RANGE', 'nama' => 'Out Range']);

        $this->createAttendance($inside, '2026-03-31');
        $this->createAttendance($inside, '2026-04-02');
        $this->createAttendance($outside, '2026-03-30');
        $this->createAttendance($outside, '2026-04-03');

        $range = ['tanggal_mulai' => '2026-03-31', 'tanggal_selesai' => '2026-04-02'];

        $this->get(route('admin.rekap', $range))
            ->assertOk()
            ->assertSee('IN-RANGE')
            ->assertDontSee('OUT-RANGE')
            ->assertSee('value="2026-03-31"', false)
            ->assertSee('value="2026-04-02"', false);

        $this->get(route('admin.export.excel', $range))
            ->assertOk()
            ->assertSee('31/03')
            ->assertSee('01/04')
            ->assertSee('02/04')
            ->assertDontSee('30/03')
            ->assertDontSee('03/04');
    }

    public function test_rekap_and_export_reject_a_date_range_with_end_before_start(): void
    {
        $this->actingAs(Karyawan::findOrFail(1), 'karyawan');

        $range = ['tanggal_mulai' => '2026-04-02', 'tanggal_selesai' => '2026-03-31'];

        $this->from(route('admin.rekap'))
            ->get(route('admin.rekap', $range))
            ->assertRedirect(route('admin.rekap'))
            ->assertSessionHasErrors('tanggal_selesai');

        $this->from(route('admin.rekap'))
            ->get(route('admin.export.excel', $range))
            ->assertRedirect(route('admin.rekap'))
            ->assertSessionHasErrors('tanggal_selesai');
    }

    private function createAttendance(Karyawan $karyawan, string $tanggal): void
    {
        Presensi::create([
            'karyawan_id' => $karyawan->id,
            'lokasi_id' => 1,
            'tanggal' => $tanggal,
            'tipe' => 'masuk',
            'jam' => '08:00:00',
            'foto_selfie' => 'test.jpg',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jarak_meter' => 10,
            'radius_diizinkan' => 100,
            'status' => 'tepat_waktu',
        ]);
    }
}
