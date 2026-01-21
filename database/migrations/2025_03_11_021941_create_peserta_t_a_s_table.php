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
        Schema::create('peserta_t_a_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mahasiswa')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('id_kelompok_ta')->nullable()->constrained('kelompok_t_a_s')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_t_a_s');
    }
};
