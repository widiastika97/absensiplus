<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->unsignedBigInteger('target_approver_id')->comment('ID Approver yang dipilih pemohon');
            $table->string('target_approver_name', 150);
            $table->enum('kategori', ['tahunan', 'khusus'])->default('tahunan');
            $table->enum('tipe_cuti', ['tahunan', 'sakit', 'melahirkan', 'izin'])->default('tahunan');
            $table->string('label_tipe', 100);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('total_hari')->default(1);
            $table->boolean('potong_kuota_tahunan')->default(true);
            $table->text('alasan');
            $table->string('lampiran_dokumen')->nullable()->comment('File di uploads/documents');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->string('approved_by_name', 150)->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
            $table->index('karyawan_id');
            $table->index('target_approver_id');
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti_karyawan');
    }
};
