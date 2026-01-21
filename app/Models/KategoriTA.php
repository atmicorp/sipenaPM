<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriTA extends Model
{
    protected $table = 'kategori_t_a_s'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function PesertaTA()
    {
        return $this->hasMany(PesertaTA::class, 'id_kategori_ta'); 
      
    }
    public function JadwalTA()
    {
        return $this->hasMany(JadwalTA::class, 'id_kategori_ta'); 
      
    }

    public function AspekPenilaianTA()
    {
        return $this->hasMany(AspekPenilaianTA::class, 'id_kategori_ta'); 
      
    }

    public function AspekPenilaianTAIndividu()
    {
        return $this->hasMany(AspekPenilaianTAIndividu::class, 'id_kategori_ta'); 
      
    }

    public function VerifikasiKelompoklTA()
    {
        return $this->hasMany(VerifikasiKelompokTA::class, 'id_kategori_ta'); 
      
    }
    
    
}
