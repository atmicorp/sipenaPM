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
        Schema::create('penilaian_t_aindividus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_data_pengujiTA')->constrained('data_penguji_tas')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('id_aspekTA_individu')->constrained('aspek_penilaian_t_a_individus')->onDelete('cascade'); // Foreign key ke tabel users
            $table->string('id_kategori_TA')->nullable(); //didapat dari relasi dengan pengujiTA
            $table->string('id_dosen')->nullable(); //didapat dari relasi dengan pengujiTA
            $table->string('id_mahasiswa')->nullable(); //didapat dari relasi dengan pengujiTA
            $table->longText("nilai")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_t_aindividus');
    }
};
