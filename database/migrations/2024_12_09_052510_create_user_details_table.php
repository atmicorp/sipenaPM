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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id(); // Primary key untuk tabel user_details (auto-increment)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->string("nim")->nullable(); //untuk user mahasiswa
            $table->string("nik")->nullable(); //untuk user dosen
            $table->string("nidn")->nullable(); //untuk user dosen
            $table->string("gelar_depan")->nullable(); //untuk user dosen
            $table->string("gelar_belakang")->nullable(); //untuk user dosen
            $table->string("jabatan")->nullable(); //untuk user dosen
            $table->longText("photo")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
