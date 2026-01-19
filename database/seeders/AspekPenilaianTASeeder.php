<?php

namespace Database\Seeders;

use App\Models\AspekPenilaianTA;
use App\Models\DataAspekPenilaianSP;
use App\Models\KategoriTA;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AspekPenilaianTASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategorilist = KategoriTA::get();

        foreach($kategorilist as $ktgr)

        AspekPenilaianTA::create([
            'aspek_penilaian' => 'Catatan Revisi',
            'porsi_penilaian' => '0',
            'deskripsi_penilaian' => null,
            'tipedata' => 'Deskripsi',
            'id_kategori_ta' => $ktgr->id,
        ]);
       
    }
}
