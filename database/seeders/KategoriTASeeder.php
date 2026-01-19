<?php

namespace Database\Seeders;

use App\Models\KategoriTA;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriTASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KategoriTA::create([
            'nama_kategori' => 'Sidang Proposal',
        ]);
        KategoriTA::create([
            'nama_kategori' => 'Sidang Seminar Hasil',
        ]);
        KategoriTA::create([
            'nama_kategori' => 'Sidang Pendadaran',
        ]);
        
    }
}
