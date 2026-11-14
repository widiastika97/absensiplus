<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_shift_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('master_shift')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['karyawan_id', 'tanggal'], 'uq_karyawan_tanggal');
            $table->index('tanggal', 'idx_jsk_tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shift_karyawan');
    }
};
