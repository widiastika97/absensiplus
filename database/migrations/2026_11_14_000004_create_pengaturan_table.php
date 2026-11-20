<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('kunci', 100)->unique();
            $table->string('nilai', 255)->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('pengaturan')->insert([
            ['kunci' => 'jam_karyawan_tetap_masuk', 'nilai' => '07:30'],
            ['kunci' => 'jam_karyawan_tetap_pulang', 'nilai' => '14:30'],
            ['kunci' => 'jam_karyawan_kontrak_masuk', 'nilai' => '07:15'],
            ['kunci' => 'jam_karyawan_kontrak_pulang', 'nilai' => '15:00'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
