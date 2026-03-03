<?php

namespace Database\Seeders;

use App\Models\StatusDosen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //    StatusDosen::create([
    //         'status_dosen' => 'Ketua Penguji',
    //     ]); 
       
    //     StatusDosen::create([
    //         'status_dosen' => 'Anggota Penguji',
    //     ]); 
        $this->call([
            KelompokTASeeder::class,
            // KategoriTASeeder::class,
            // PesertaTASeeder::class,
            JadwalTASeeder::class,
            // AspekPenilaianTASeeder::class,
            VerifikasiKelompokTAseeder::class,
        ]);
    }
}
