<?php

namespace App\Imports;

use App\Models\DataPengujiMagang;
use App\Models\StatusDosen;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class DataPengujiImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        
        // Ambil id_dosen berdasarkan nik_dosen dari row
        $dosen = UserDetail::where('nik', $row['nik_dosen'])->first(); // Pastikan nik_dosen ada di kolom yang benar
        if (!$dosen) {
            throw ValidationException::withMessages([
                'error' => "Dosen dengan NIK " . $row['nik_dosen'] . " pada sheet 2, tidak sesuai dengan sheet 1."
            ]);
        }
        
        $id_dosen = $dosen->user_id;
        
        // Ambil id_mahasiswa berdasarkan nim_mahasiswa dari row
        $mahasiswa = UserDetail::where('nim', $row['nim_mahasiswa'])->first(); // Pastikan nim_mahasiswa ada di kolom yang benar
        if (!$mahasiswa) {
            throw ValidationException::withMessages([
                'error' => "Mahasiswa dengan NIM " . $row['nim_mahasiswa'] . " pada sheet 2, tidak sesuai dengan sheet 1."
            ]);
        }
        $id_mahasiswa = $mahasiswa->user_id; // Jika mahasiswa ditemukan, ambil ID-nya


        // collect()->pluck($value, $key); pluck secara default hanya berisi 2 parameter.
        // cocok digunakan untuk validasi antar tabel
        // dalam kasus ini brarti, id sebagai value, dan status_dosen sebagai key, 
        //value dipanggil pertama, key dipangggil ke dua pada parameter

        $statsdos = StatusDosen::pluck('id', 'status_dosen');
       // Cek apakah status_dosen di $row ada dalam daftar status dosen
        if (!isset($statsdos[$row['status_dosen']])) {
            throw ValidationException::withMessages([
                'error' => "Status Dosen " . $row['status_dosen'] . " tidak valid pada NIK dosen" .  $row['nik_dosen']
            ]);
        }
        // Jika ada, ambil ID yang sesuai
        $idstatusdosen = $statsdos[$row['status_dosen']];
         
        // Simpan data ke tabel `data_penguji_magang`
        return DataPengujiMagang::create([
            'id_dosen'         => $id_dosen,
            'id_mahasiswa'     => $id_mahasiswa,
            'status_dosen'     => $idstatusdosen
        ]);
    }
}
