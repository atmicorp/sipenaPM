<?php

namespace Database\Seeders;

use App\Models\JadwalTA;
use App\Models\KategoriTA;
use App\Models\KelompokTA;
use App\Models\PesertaTA;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalTASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user yang memiliki role mahasiswa
        $kelompoktaList = KelompokTA::all(); 

        $kategoriList = KategoriTA::pluck('id'); // Mengambil hanya ID kategori
        
        foreach ($kelompoktaList as $kelompokta) {
            foreach ($kategoriList as $kategoriId) {
               JadwalTA::create([
                    'id_kelompok_ta' => $kelompokta->id,
                    'id_kategori_ta' => $kategoriId,  
                    'tanggal_presentasi' => null, 
                    'jam_presentasi' => null, 
                    'jam_presentasi_selesai' => null,  
                    'lokasi' => null,  
                ]);
            }
        }
    }
}
