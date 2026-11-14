<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_kantor', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lokasi', 50)->unique();
            $table->string('nama_lokasi', 150);
            $table->text('alamat');
            $table->string('kota', 100);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->unsignedInteger('radius')->default(50)->comment('Radius toleransi dalam satuan meter');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_kantor');
    }
};
