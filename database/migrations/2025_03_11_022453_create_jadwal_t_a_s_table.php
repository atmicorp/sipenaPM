<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_t_a_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kelompok_ta')->constrained('kelompok_t_a_s')->onDelete('cascade');
            $table->foreignId('id_kategori_ta')->constrained('kategori_t_a_s')->onDelete('cascade'); // Foreign key ke tabel users
            $table->string("tanggal_presentasi")->nullable();
            $table->string("jam_presentasi")->nullable();
            $table->string("jam_presentasi_selesai")->nullable();
            $table->string("lokasi")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_t_a_s');
    }
};
