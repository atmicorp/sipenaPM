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
        Schema::create('data_penguji_tas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_dosen')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users 
            $table->foreignId('id_kelompok_ta')->constrained('kelompok_t_a_s')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('status_dosen')->constrained('status_dosens')->onDelete('cascade'); // Foreign key ke tabel users 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_penguji_tas');
    }
};
