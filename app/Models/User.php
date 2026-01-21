<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable

{
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function details()
    {
        return $this->hasOne(UserDetail::class, 'user_id'); 
        //user_id pada tabel user_details adalah foreign key yang merujuk ke kolom id pada tabel users
    }

    public function pesertamagang()
    {
        return $this->hasOne(PesertaMagang::class, 'id_mahasiswa'); 
        //id_mahasiswa pada tabel peserta_magangs adalah foreign key yang merujuk ke kolom id pada tabel users
    }

    public function mahasiswaBimbingan()
    {
        return $this->belongsToMany(DataPengujiMagang::class, 'id_mahasiswa');
    }
    
    public function dosenPenguji()
    {
        return $this->belongsToMany(DataPengujiMagang::class, 'id_dosen');
    }

    public function dosenPengujiTA()
    {
        return $this->belongsToMany(DataPengujiTa::class, 'id_dosen');
    }

    public function pesertaTA()
    {
        return $this->hasMany(PesertaTA::class, 'id_mahasiswa'); 
        //id_mahasiswa pada tabel peserta_magangs adalah foreign key yang merujuk ke kolom id pada tabel users
    }
}
