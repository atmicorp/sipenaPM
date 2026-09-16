<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_penguji_tas', function (Blueprint $table) {
            $table->foreignId('id_kategori_ta')
                ->nullable()
                ->after('id_kelompok_ta')
                ->constrained('kategori_t_a_s') // sesuaikan nama tabel kategori TA-mu
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('data_penguji_tas', function (Blueprint $table) {
            $table->dropForeign(['id_kategori_ta']);
            $table->dropColumn('id_kategori_ta');
        });
    }
};