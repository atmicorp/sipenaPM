<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianTAindividu extends Model
{
    use HasFactory;
    protected $table = 'penilaian_t_aindividus'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function aspekpenilaianTAindividu()
    {
        return $this->belongsTo(aspekpenilaianTAindividu::class, 'id_aspekTAindividu');  
    }

    public function pengujiTAindividu()
    {
        return $this->belongsTo(DataPengujiTa::class, 'id_data_pengujiTA');  
    }
}
