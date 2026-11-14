<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_lainnya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->enum('tipe_pengajuan', ['terlambat', 'pulang_cepat']);
            $table->string('label_tipe', 100);
            $table->date('tanggal')->comment('Tanggal Kejadian')->index();
            $table->time('jam_estimasi');
            $table->unsignedBigInteger('target_approver_id');
            $table->string('target_approver_name', 150);
            $table->text('alasan');
            $table->string('foto_bukti')->nullable()->comment('File Foto Bukti di uploads/documents');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->string('approved_by_name', 150)->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
            $table->index('karyawan_id', 'idx_pl_karyawan');
            $table->index('target_approver_id', 'idx_pl_approver');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_lainnya');
    }
};
