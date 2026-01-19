<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    // use HasFactory;
    protected $table = 'user_details'; // Menentukan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id'; // Menentukan kolom primary key yang digunakan (defaultnya Laravel adalah 'id')
    protected $guarded = ['id']; // Menentukan kolom yang tidak boleh diisi mass-assignment. Di sini, hanya kolom 'id' yang tidak bisa diisi.

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
        //model UserDetail milik (belongs to) model User, dan foreign key yang digunakan adalah user_id di tabel user_details
    }
}
