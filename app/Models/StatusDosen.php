<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusDosen extends Model
{
    use HasFactory;
    protected $table = 'status_dosens'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function pengujimagang()
    {
        return $this->hasMany(DataPengujiMagang::class, 'status_dosen');  
    }

    public function pengujiTA()
    {
        return $this->hasMany(DataPengujiTa::class, 'status_dosen');  
    }

}
