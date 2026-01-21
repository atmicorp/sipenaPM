<?php

namespace Database\Seeders;

use App\Models\AspekPenilaianTAIndividu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AspekPenilaianTASeederIndividu extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AspekPenilaianTAIndividu::create([
            'aspek_penilaian' => 'Catatan Revisi',
            'porsi_penilaian' => '0',
            'deskripsi_penilaian' => null,
            'tipedata' => 'Deskripsi',
        
        ]);

        
    }
}
