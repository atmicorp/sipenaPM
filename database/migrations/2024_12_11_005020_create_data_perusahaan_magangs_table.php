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
        Schema::create('data_perusahaan_magangs', function (Blueprint $table) {
            $table->id();
            $table->string("nama")->nullable(); //untuk nama perusahaan
            $table->string("alamat")->nullable(); //untuk nama perusahaan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_perusahaan_magangs');
    }
};
