<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Sheet1' => new UsersImport(), // Nama sheet pertama
            'Sheet2' => new DataPengujiImport(), // Nama sheet kedua
            'Sheet3' => new DataPerusahaanImport(), // Nama sheet ketiga
            'Sheet4' => new DataPesertaMagangImport(), // Nama sheet keempat
        ];
    }
}
