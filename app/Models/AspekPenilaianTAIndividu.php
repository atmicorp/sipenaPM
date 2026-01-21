<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AspekPenilaianTAIndividu extends Model
{
    use HasFactory;
    protected $table = 'aspek_penilaian_t_a_individus'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.


    public function kategoriTA()
    {
        return $this->belongsTo(KategoriTA::class, 'id_kategori_ta'); 
        //model UserDetail milik (belongs to) model User, dan foreign key yang digunakan adalah user_id di tabel user_details
    }
    public function penilaianTAindividu()
    {
        return $this->hasMany(PenilaianTAindividu::class, 'id_aspekTAindividu');  
    }
}
