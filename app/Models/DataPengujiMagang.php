<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPengujiMagang extends Model
{
  
    use HasFactory;
    protected $table = 'data_penguji_magangs'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.


    public function usermahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa'); 
    }

    public function userdosen()
    {
        return $this->belongsTo(User::class, 'id_dosen'); 
    }

    public function status()
    {
        return $this->belongsTo(StatusDosen::class, 'status_dosen');  
    }

    public function aspekpenilaianmagang()
    {
        return $this->hasMany(PenilaianMagang::class, 'id_data_penguji_magangs');  
    }

   
}


