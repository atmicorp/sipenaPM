<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAspekPenilaianMagang extends Model
{
    
    use HasFactory;
    protected $table = 'data_aspek_penilaian_magangs'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function aspekpenilaianmagang()
    {
        return $this->hasMany(PenilaianMagang::class, 'id_aspek_penilaian');  
    }
}
