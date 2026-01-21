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
        Schema::create('kelompok_t_a_s', function (Blueprint $table) {
            $table->id();
            $table->string("nama_kelompok")->nullable();
            $table->longText("judul_ta")->nullable();
            $table->string("sk")->nullable();
            $table->string("tahun_perkuliahan")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_t_a_s');
    }
};
