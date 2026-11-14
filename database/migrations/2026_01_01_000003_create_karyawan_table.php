<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 50)->unique();
            $table->string('nama', 150);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('departemen', 100);
            $table->string('jabatan', 100);
            $table->foreignId('role_id')->default(4)->constrained('roles')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'])->default('Islam');
            $table->string('no_hp', 20)->nullable();
            $table->string('foto_profil')->default('default-avatar.png');
            $table->time('jam_masuk')->default('08:00:00');
            $table->time('jam_pulang')->default('17:00:00');
            $table->unsignedInteger('kuota_cuti_tahunan')->default(12)->comment('Jatah Kuota Saldo Awal Cuti Tahunan Karyawan per Tahun (Hari)');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
