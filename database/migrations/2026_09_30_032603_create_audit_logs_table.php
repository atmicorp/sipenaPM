<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: kalau tabel sudah ada, tidak melakukan apa pun.
        if (Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->string('jenis', 20);   // keamanan | data | nilai
            $table->string('aksi', 100);   // contoh: login_berhasil, ganti_foto, hapus_penguji

            // Pelaku. Sengaja TANPA foreign key: log harus tetap ada
            // walaupun user-nya dihapus (misalnya lewat reset database).
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_email')->nullable(); // salinan email saat kejadian

            // Data yang terkena aksi
            $table->string('target_tipe', 100)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();

            $table->string('deskripsi', 500);
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();

            // Asal permintaan
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('metode', 10)->nullable();
            $table->string('url', 1000)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();

            // Hanya created_at, log tidak pernah diubah
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index('user_id');
            $table->index('jenis');
            $table->index('aksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};