<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Membaca file export dari logbook (satu sheet, satu baris per internship)
 * apa adanya. Tidak ada validasi di sini; validasi dilakukan oleh
 * LogbookImportService.
 */
class LogbookRawImport implements ToCollection, WithHeadingRow
{
    /** @var array<int, array<string, mixed>> */
    public array $rows = [];

    /**
     * Kolom yang wajib ada di baris heading file.
     */
    public const KOLOM_WAJIB = [
        'name',
        'email',
        'nim',
        'nama_perusahaan',
        'email_instruktur',
    ];

    public function collection(Collection $collection): void
    {
        $this->rows = $collection->map(fn ($row) => $row->toArray())->values()->all();
    }

    /**
     * Cek apakah heading file memuat semua kolom wajib.
     * Mengembalikan daftar kolom yang hilang (kosong = lengkap).
     */
    public function kolomHilang(): array
    {
        if (empty($this->rows)) {
            return self::KOLOM_WAJIB;
        }

        $ada = array_keys($this->rows[0]);

        return array_values(array_diff(self::KOLOM_WAJIB, $ada));
    }
}