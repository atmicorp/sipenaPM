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
        Schema::create('data_aspek_penilaian_magangs', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('data_aspek_penilaian_magangs');
    }
};
