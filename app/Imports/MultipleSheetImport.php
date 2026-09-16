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
            // Sheet2 (data penguji/pembimbing magang) sengaja TIDAK dipetakan lagi.
            // Penugasan dosen pembimbing & penguji sekarang dilakukan manual di
            // sistem lewat halaman "Set Up Data Praktik Kerja" (storedatapembimbing),
            // bukan lagi dari file Excel saat upload data awal.
            'Sheet1' => new UsersImport(),           // Nama sheet pertama (User: mahasiswa & dosen)
            'Sheet2' => new DataPerusahaanImport(),  // Nama sheet ketiga (data perusahaan)
            'Sheet3' => new DataPesertaMagangImport(),// Nama sheet keempat (peserta magang)
        ];
    }
}