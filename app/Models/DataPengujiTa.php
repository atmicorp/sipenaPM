<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPengujiTa extends Model
{
    use HasFactory;
    protected $table = 'data_penguji_tas'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function KelompokTA()
    {
        return $this->belongsTo(KelompokTA::class, 'id_kelompok_ta'); 
    }

    public function userdosenTA()
    {
        return $this->belongsTo(User::class, 'id_dosen'); 
    }

    public function statusdosenTA()
    {
        return $this->belongsTo(StatusDosen::class, 'status_dosen');  
    }
    public function penilaianTA()
    {
        return $this->hasMany(penilaianTA::class, 'id_data_pengujiTA');  
    }
    public function penilaianTAindividu()
    {
        return $this->hasMany(penilaianTAindividu::class, 'id_data_pengujiTA');  
    }
    // public function aspekpenilaianSeminarHasil()
    // {
    //     return $this->hasMany(PenilaianMagang::class, 'id_data_penguji_magangs');  
    // }
    // public function aspekpenilaianPendadaran()
    // {
    //     return $this->hasMany(PenilaianMagang::class, 'id_data_penguji_magangs');  
    // }
}
