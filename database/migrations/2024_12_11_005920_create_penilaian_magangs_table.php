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
        Schema::create('penilaian_magangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_data_penguji_magangs')->constrained('data_penguji_magangs')->onDelete('cascade'); // Foreign key ke tabel users
            $table->string('id_dosen')->nullable();
            $table->string('id_mahasiswa')->nullable();
            $table->foreignId('id_aspek_penilaian')->constrained('data_aspek_penilaian_magangs')->onDelete('cascade'); // Foreign key ke tabel users
            $table->longText("nilai")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_magangs');
    }
};
