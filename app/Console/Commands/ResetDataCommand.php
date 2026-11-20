<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ResetDataCommand extends Command
{
    protected $signature = 'absensi:reset
        {--data-only : Hanya kosongkan data transaksional (presensi, cuti, roster); master tetap}
        {--force : Lewati konfirmasi}';

    protected $description = 'Hapus dan generate ulang isi database aplikasi absensi (termasuk membersihkan kode role lama)';

    public function handle(): int
    {
        $this->warn('PERINGATAN: Tindakan ini akan MENGHAPUS DATA dan tidak dapat dibatalkan.');

        if ($this->option('data-only')) {
            return $this->resetDataOnly();
        }

        $this->info('Seluruh tabel akan di-drop lalu dibuat ulang (migrate:fresh --seed).');
        $this->line('Roles generik (Karyawan Tetap/Kontrak/Honor), pengaturan jam kerja, dan data demo karyawan akan dibuat ulang.');

        if (! $this->option('force') && ! $this->confirm('Lanjutkan reset penuh database?')) {
            $this->info('Dibatalkan.');

            return self::FAILURE;
        }

        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $this->line(Artisan::output());

        $this->info('Database berhasil direset dan di-generate ulang.');

        return self::SUCCESS;
    }

    private function resetDataOnly(): int
    {
        $tabel = ['presensi', 'cuti_karyawan', 'pengajuan_lainnya', 'jadwal_shift_karyawan'];
        $this->info('Tabel yang akan dikosongkan: '.implode(', ', $tabel));

        if (! $this->option('force') && ! $this->confirm('Lanjutkan mengosongkan tabel data?')) {
            $this->info('Dibatalkan.');

            return self::FAILURE;
        }

        $isMySql = in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        if ($isMySql) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($tabel as $t) {
            DB::statement($isSqlite ? "DELETE FROM {$t}" : "TRUNCATE TABLE {$t}");
            $this->line("  dikosongkan: {$t}");
        }

        if ($isMySql) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->info('Selesai. Master (roles, karyawan, shift, lokasi, pengaturan) tetap utuh.');

        return self::SUCCESS;
    }
}
