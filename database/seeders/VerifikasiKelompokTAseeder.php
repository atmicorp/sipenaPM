<?php

namespace Database\Seeders;

use App\Models\KategoriTA;
use App\Models\KelompokTA;
use App\Models\VerifikasiKelompokTA;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VerifikasiKelompokTAseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelompokta = KelompokTA::get();
        $kategorita = KategoriTA::get();

        foreach ($kelompokta as $kelta) {
            foreach ($kategorita as $kategori) {
                VerifikasiKelompokTA::create([
                    'id_kelompok_ta' => $kelta->id,
                    'id_kategori_ta' => $kategori->id,
                    'status' => '0',
                ]);
            }
        }
    }
}
