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
        Schema::create('aspek_penilaian_t_a_individus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kategori_ta')->constrained('kategori_t_a_s')->onDelete('cascade'); // Foreign key ke tabel users
            $table->string("aspek_penilaian")->nullable(); //untuk nama perusahaan
            $table->longText("deskripsi_penilaian")->nullable(); //untuk nama perusahaan
            $table->string("porsi_penilaian")->nullable(); //untuk nama perusahaan
            $table->string("tipedata")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspek_penilaian_t_a_individus');
    }
};
