<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianTA extends Model
{
    use HasFactory;
    protected $table = 'penilaian_t_a_s'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function aspekpenilaianTA()
    {
        return $this->belongsTo(AspekPenilaianTA::class, 'id_aspekTA');  
    }

    public function pengujiTA()
    {
        return $this->belongsTo(DataPengujiTa::class, 'id_data_pengujiTA');  
    }
}
