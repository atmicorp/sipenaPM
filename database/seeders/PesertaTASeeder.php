<?php

namespace Database\Seeders;

use App\Models\KategoriTA;
use App\Models\PesertaTA;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PesertaTASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user yang memiliki role mahasiswa
        $mahasiswaList = User::role('Mahasiswa')->get();

      
        
        foreach ($mahasiswaList as $mahasiswa) {
            
                PesertaTA::create([
                    'id_mahasiswa' => $mahasiswa->id,
                    'id_kelompok_ta' => null,   
                ]);
            
        }
    }
}
