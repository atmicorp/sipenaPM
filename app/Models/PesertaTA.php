<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaTA extends Model
{
   
    protected $table = 'peserta_t_a_s'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function usermahasiswaTA()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa'); 
        //model UserDetail milik (belongs to) model User, dan foreign key yang digunakan adalah id_mahasiswa di tabel peserta_magangs
    }

    public function kategoriTA()
    {
        return $this->belongsTo(KategoriTA::class, 'id_kategori_ta'); 
        //model UserDetail milik (belongs to) model User, dan foreign key yang digunakan adalah id_mahasiswa di tabel peserta_magangs
    }

    public function kelompokTA()
    {
        return $this->belongsTo(KelompokTA::class, 'id_kelompok_ta'); 
        //model UserDetail milik (belongs to) model User, dan foreign key yang digunakan adalah id_mahasiswa di tabel peserta_magangs
    }

}
