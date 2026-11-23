<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('catatan');
            $table->unsignedBigInteger('admin_id')->comment('Admin yang menambahkan catatan');
            $table->string('admin_name', 150);
            $table->timestamps();
            $table->unique(['karyawan_id', 'tanggal']);
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_absensi');
    }
};
