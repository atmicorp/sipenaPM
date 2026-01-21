<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiKelompokTA extends Model
{
    protected $table = 'verifikasi_kelompok_t_a_s'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function KelompokTA()
    {
        return $this->belongsTo(KelompokTA::class, 'id_kelompok_ta'); 
      
    }

    public function KategoriTA()
    {
        return $this->belongsTo(KelompokTA::class, 'id_kategori_ta'); 
      
    }
}
