<?php

namespace App\Imports;

use App\Models\DataPerusahaanMagang;
use App\Models\PesertaMagang;
use App\Models\UserDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class DataPesertaMagangImport implements ToModel, WithHeadingRow
{
     /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        
        $perusahaan = DataPerusahaanMagang::where('nama', $row['nama_perusahaan'])->first(); // Pastikan nik_perusahaan ada di kolom yang benar
        if (!$perusahaan) {
            throw ValidationException::withMessages([
                'error' => "Nama Perusahaan " . $row['nama_perusahaan'] . " pada sheet 4, tidak sesuai dengan sheet 3."
            ]);
        }
        $id_perusahaan =  $perusahaan->id; // Jika perusahaan ditemukan, ambil ID-nya, jika tidak, set null

        $mahasiswa = UserDetail::where('nim', $row['nim_mahasiswa'])->first(); // Pastikan nim_mahasiswa ada di kolom yang benar
        if (!$mahasiswa) {
            throw ValidationException::withMessages([
                'error' => "Mahasiswa dengan NIM " . $row['nim_mahasiswa'] . " pada sheet 4, tidak sesuai dengan sheet 1."
            ]);
        }
        $id_mahasiswa = $mahasiswa->user_id; // Jika mahasiswa ditemukan, ambil ID-nya, jika tidak, set null
        
        if (is_numeric($row['tanggal_presentasi'])) {
            $tanggal_presentasi = Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($row['tanggal_presentasi'] - 2)->format('m/d/Y');
        } else {
            // Jika sudah dalam format yang benar
            $tanggal_presentasi = $row['tanggal_presentasi'];
        }

        if (is_numeric($row['jam_presentasi'])) {
            $jam_presentasi = Carbon::createFromTimeString('00:00:00') // Mulai dari tengah malam
                ->addHours(floor($row['jam_presentasi'] * 24)) // Tambahkan jam dari desimal
                ->addMinutes(($row['jam_presentasi'] * 1440) % 60) // Tambahkan menit dari desimal
                ->format('H:i:s'); // Format ke H:i:s sesuai kebutuhan database
        } else {
            $jam_presentasi = $row['jam_presentasi'];
        }

        if (is_numeric($row['jam_presentasi_selesai'])) {
            $jam_presentasi_selesai = Carbon::createFromTimeString('00:00:00') // Mulai dari tengah malam
                ->addHours(floor($row['jam_presentasi_selesai'] * 24)) // Tambahkan jam dari desimal
                ->addMinutes(($row['jam_presentasi_selesai'] * 1440) % 60) // Tambahkan menit dari desimal
                ->format('H:i:s'); // Format ke H:i:s sesuai kebutuhan database
        } else {
            $jam_presentasi_selesai = $row['jam_presentasi_selesai'];
        }

        
    
        return PesertaMagang::create([
            'id_mahasiswa'     => $id_mahasiswa,
            'id_perusahaan'     => $id_perusahaan,
            'tanggal_presentasi'     => $tanggal_presentasi,
            'jam_presentasi'     => $jam_presentasi,
            'jam_presentasi_selesai'     => $jam_presentasi_selesai,
            'lokasi' => isset($row['lokasi']) ? $row['lokasi'] : null,  // Gunakan null atau nilai default jika 'lokasi' tidak ada
   
        ]);

    }
}
