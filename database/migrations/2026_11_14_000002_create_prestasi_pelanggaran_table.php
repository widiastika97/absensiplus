<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['prestasi', 'pelanggaran']);
            $table->enum('subjek', ['siswa', 'guru']);
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('karyawan')->cascadeOnDelete();
            $table->string('nama_manual', 150)->nullable()->comment('Nama teks bebas bila tanpa referensi guru/siswa');
            $table->string('judul', 200)->comment('Nama prestasi atau pelanggaran');
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->foreignId('pelapor_id')->constrained('karyawan')->cascadeOnDelete();
            $table->timestamps();

            $table->index('kategori');
            $table->index('subjek');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_pelanggaran');
    }
};
