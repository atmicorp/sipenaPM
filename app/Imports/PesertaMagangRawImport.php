<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Sama seperti MultipleSheetImport, tapi HANYA membaca isi file mentahnya
 * (array biasa) tanpa memicu proses simpan ke database sama sekali.
 * Cocok berdasarkan NAMA sheet ("Sheet1", "Sheet2", "Sheet3"), jadi sheet
 * tambahan seperti "Petunjuk" pada template otomatis diabaikan.
 */
class PesertaMagangRawImport implements WithMultipleSheets
{
    public RawSheetCollector $sheet1;
    public RawSheetCollector $sheet2;
    public RawSheetCollector $sheet3;

    public function __construct()
    {
        $this->sheet1 = new RawSheetCollector();
        $this->sheet2 = new RawSheetCollector();
        $this->sheet3 = new RawSheetCollector();
    }

    public function sheets(): array
    {
        return [
            'Sheet1' => $this->sheet1, // Data Mahasiswa
            'Sheet2' => $this->sheet2, // Data Perusahaan Magang
            'Sheet3' => $this->sheet3, // Penempatan Peserta Magang
        ];
    }
}