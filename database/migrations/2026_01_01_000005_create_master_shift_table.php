<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_shift', function (Blueprint $table) {
            $table->id();
            $table->string('kode_shift', 50)->unique();
            $table->string('nama_shift', 100);
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->boolean('is_night_shift')->default(false)->comment('1 = Shift malam / lintas hari');
            $table->boolean('is_off')->default(false)->comment('1 = Jadwal Libur Shift / Day Off');
            $table->string('warna_badge', 30)->default('blue');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_shift');
    }
};
