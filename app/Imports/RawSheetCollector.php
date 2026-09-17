<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Membaca satu sheet menjadi array asosiatif biasa, TANPA menyentuh database
 * sama sekali. Dipakai untuk tahap preview (dry-run) maupun tahap commit
 * (setelah admin klik "Simpan") dari fitur Import Peserta Magang.
 */
class RawSheetCollector implements ToCollection, WithHeadingRow
{
    /** @var array<int, array<string, mixed>> */
    public array $rows = [];

    public function collection(Collection $rows)
    {
        $this->rows = $rows
            ->map(fn ($row) => $row->toArray())
            ->values()
            ->toArray();
    }
}