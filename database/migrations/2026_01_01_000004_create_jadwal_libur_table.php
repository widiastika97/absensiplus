<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_libur', function (Blueprint $table) {
            $table->id();
            $table->string('nama_libur', 150);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('tipe_target', ['semua', 'karyawan', 'agama'])->default('semua');
            $table->enum('target_agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'])->nullable();
            $table->json('target_karyawan_ids')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_libur');
    }
};
