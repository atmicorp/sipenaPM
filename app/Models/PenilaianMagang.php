<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianMagang extends Model
{
    use HasFactory;
    protected $table = 'penilaian_magangs'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function aspekpenilaianmagang()
    {
        return $this->belongsTo(DataAspekPenilaianMagang::class, 'id_aspek_penilaian');  
    }

    public function pengujimagang()
    {
        return $this->belongsTo(DataPengujiMagang::class, 'id_data_penguji_magangs');  
    }

}
