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
        Schema::create('peserta_magangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mahasiswa')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('id_perusahaan')->constrained('data_perusahaan_magangs')->onDelete('cascade'); // Foreign key ke tabel users
            $table->string("tanggal_presentasi")->nullable();
            $table->string("jam_presentasi")->nullable();
            $table->string("jam_presentasi_selesai")->nullable();
            $table->string("lokasi")->nullable();
            $table->string("judul_laporan")->nullable();
            $table->string("tahun")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_magangs');
    }
};
