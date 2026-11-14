<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasi_kantor')->restrictOnDelete();
            $table->date('tanggal')->index();
            $table->enum('tipe', ['masuk', 'pulang']);
            $table->time('jam');
            $table->string('foto_selfie')->comment('Nama file foto selfie jpg di storage uploads/selfie');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('jarak_meter', 8, 2);
            $table->unsignedInteger('radius_diizinkan');
            $table->enum('status', ['tepat_waktu', 'terlambat', 'lembur', 'dini'])->default('tepat_waktu');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->index('karyawan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
