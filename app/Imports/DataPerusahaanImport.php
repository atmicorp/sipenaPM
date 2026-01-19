<?php

namespace App\Imports;

use App\Models\DataPerusahaanMagang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class DataPerusahaanImport implements ToModel, WithHeadingRow
{
    
    public function model(array $row)
    {
         // Simpan data ke tabel `perusahaan`
         $perusahaan = DataPerusahaanMagang::create([
            'nama'     => $row['nama'], // Kolom 'name' dari header Excel
            'alamat'     => $row['alamat'], 
        ]);
        return $perusahaan; // Mengembalikan objek perusahaan
    }
}
