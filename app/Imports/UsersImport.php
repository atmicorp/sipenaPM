<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * Method model untuk memproses setiap baris dari Excel
     * 
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
     
        // Cek apakah user dengan email ini sudah ada
        $existingUser = User::where('email', $row['email'])->first();

        if ($existingUser) {
            return null; // Jika sudah ada, abaikan input
        }
        
        // Simpan data ke tabel `users`
        $user = User::create([
            'name'     => $row['name'], // Kolom 'name' dari header Excel
            'email'    => $row['email'], // Kolom 'email' dari header Excel
            'password' => Hash::make($row['password']), // Kolom 'password' dari header Excel
        ]);

        // Simpan data ke tabel `user_details`
        UserDetail::create([
            'user_id'         => $user->id,
            'nim'             => $row['nim'], // Kolom 'nim' dari header Excel
            'nik'             => $row['nik'], // Kolom 'nik' dari header Excel
            'nidn'            => $row['nidn'], // Kolom 'nidn' dari header Excel
            'gelar_depan'     => $row['gelar_depan'], // Kolom 'gelar_depan' dari header Excel
            'gelar_belakang'  => $row['gelar_belakang'], // Kolom 'gelar_belakang' dari header Excel
            'jabatan'         => $row['jabatan'], // Kolom 'jabatan' dari header Excel
            'photo'           => $row['photo'], // Kolom 'photo' dari header Excel
        ]);

        $roleName = $row['role']; // Kolom 'role' dari header Excel
        if ($roleName) {
            // Cari role berdasarkan nama
            $role = Role::where('name', $roleName)->first();
    
            // Tetapkan role pada user, data akan disimpan dalam model_has_role
            if ($role) {
                $user->assignRole($role->name);
            }
        }

        return $user; // Mengembalikan objek user

        
    }
}