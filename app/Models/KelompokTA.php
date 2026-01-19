<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokTA extends Model
{
    protected $table = 'kelompok_t_a_s'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function PesertaTA()
    {
        return $this->hasOne(PesertaTA::class, 'id_kelompok_ta'); 
      
    }
    public function JadwalTA()
    {
        return $this->hasMany(JadwalTA::class, 'id_kelompok_ta'); 
      
    }

    public function PengujiTA()
    {
        return $this->belongsToMany(DataPengujiTa::class, 'id_kelompok_ta');
    }

    public function VerifikasiKelompoklTA()
    {
        return $this->hasMany(VerifikasiKelompokTA::class, 'id_kelompok_ta'); 
      
    }
    
}
