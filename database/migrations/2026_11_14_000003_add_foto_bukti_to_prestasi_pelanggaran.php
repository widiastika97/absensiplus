<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasi_pelanggaran', function (Blueprint $table) {
            $table->string('foto_bukti')->nullable()->after('keterangan')
                ->comment('File foto bukti prestasi/pelanggaran di uploads/prestasi/');
        });
    }

    public function down(): void
    {
        Schema::table('prestasi_pelanggaran', function (Blueprint $table) {
            $table->dropColumn('foto_bukti');
        });
    }
};
