<?php

namespace Database\Seeders;

use App\Models\DataAspekPenilaianMagang;
use App\Models\DataPerusahaanMagang;
use App\Models\StatusDosen;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DataSampelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // make data role--------------
        $adminrole = Role::create([
            'name' => 'Admin'
        ]);
        $Dosenrole = Role::create([
            'name' => 'Dosen'
        ]);
        $Mahasiswarole = Role::create([
            'name' => 'Mahasiswa'
        ]);
        // end data role--------------


        // make user admin------------
        $admin = User::create([
            'name' => 'admin PM',
            'email' => 'pm@atmi.ac.id',
            'password' => Hash::make('pm.edu'),
        ]);

        UserDetail::create([
            'user_id' => $admin->id,
            'nim' => '',
            'nik' => '',
            'nidn' => '-',
            'gelar_depan' => '',
            'gelar_belakang' => '',
            'jabatan' => '',
            
        ]);

        $admin1 = User::create([
            'name' => 'admin RTM',
            'email' => 'rtm@atmi.ac.id',
            'password' => Hash::make('rtm.edu'),
        ]);

        UserDetail::create([
            'user_id' => $admin1->id,
            'nim' => '',
            'nik' => '',
            'nidn' => '-',
            'gelar_depan' => '',
            'gelar_belakang' => '',
            'jabatan' => '',
            
        ]);
        // ---------------------------

         // make user dosen------------
        //     $dosen1 = User::create([
        //         'name' => 'Abram Pangeling',
        //         'email' => 'abram.pangeling@atmi.ac.id',
        //         'password' => Hash::make('password'),
        //     ]);
        //     UserDetail::create([
        //         'user_id' => $dosen1->id,
        //         'nim' => '',
        //         'nik' => '1524/11/16',
        //         'nidn' => '9906978260',
        //         'gelar_depan' => '',
        //         'gelar_belakang' => 'S.Tr.T.',
        //         'jabatan' => 'Dosen/Instruktur',
                
        //     ]);

        //     $dosen2 = User::create([
        //         'name' => 'Adhi Setya Hutama',
        //         'email' => 'setya.hutama@atmi.ac.id',
        //         'password' => Hash::make('password'),
        //     ]);
        //     UserDetail::create([
        //         'user_id' => $dosen2->id,
        //         'nim' => '',
        //         'nik' => '1616/04/19',
        //         'nidn' => '0608108701',
        //         'gelar_depan' => '',
        //         'gelar_belakang' => 'S.T., M.Sc.',
        //         'jabatan' => 'Dosen/Instruktur',
                
        //     ]);

        //     $dosen3 = User::create([
        //         'name' => 'Paulinus Cherlyndo Paterias',
        //         'email' => 'cherlyndo.paterias@atmi.ac.id',
        //         'password' => Hash::make('password'),
        //     ]);
        //     UserDetail::create([
        //         'user_id' => $dosen3->id,
        //         'nim' => '',
        //         'nik' => '1535/02/17',
        //         'nidn' => '9906978261',
        //         'gelar_depan' => '',
        //         'gelar_belakang' => 'S.Tr.T.',
        //         'jabatan' => 'Kepala Tingkat 4',
                
        //     ]);

        //     $dosen4 = User::create([
        //         'name' => 'Wahyu Punta Rajamanggala',
        //         'email' => 'punta.rajamanggala@atmi.ac.id',
        //         'password' => Hash::make('password'),
        //     ]);
        //     UserDetail::create([
        //         'user_id' => $dosen4->id,
        //         'nim' => '',
        //         'nik' => '1563/09/17',
        //         'nidn' => '9906978271',
        //         'gelar_depan' => '',
        //         'gelar_belakang' => 'S.SI.',
        //         'jabatan' => 'Dosen/Instruktur',
                
        //     ]);
            
        // // ---------------------------


        // // Data Mahasiswa------------

        // $mahasiswa1 = User::create([
        //     'name' => 'Agustinus Valentino Sonny Nugraha',
        //     'email' => 'agustinus.20215001@student.atmi.ac.id',
        //     'password' => Hash::make('password'),
        // ]);
        // UserDetail::create([
        //     'user_id' => $mahasiswa1->id,
        //     'nim' => '20215001',
        //     'nik' => '',
        //     'nidn' => '',
        //     'gelar_depan' => '',
        //     'gelar_belakang' => '',
        //     'jabatan' => 'Mahasiswa',
            
        // ]);

        // $mahasiswa2 = User::create([
        //     'name' => 'Alfia Asia Putri Is. Hiola',
        //     'email' => 'alfia.20215002@student.atmi.ac.id',
        //     'password' => Hash::make('password'),
        // ]);
        // UserDetail::create([
        //     'user_id' => $mahasiswa2->id,
        //     'nim' => '20215002',
        //     'nik' => '',
        //     'nidn' => '',
        //     'gelar_depan' => '',
        //     'gelar_belakang' => '',
        //     'jabatan' => 'Mahasiswa',
            
        // ]);

        // $mahasiswa3 = User::create([
        //     'name' => 'Angela Merici Noni Widya Tirtha',
        //     'email' => 'angela.20215003@student.atmi.ac.id',
        //     'password' => Hash::make('password'),
        // ]);
        // UserDetail::create([
        //     'user_id' => $mahasiswa3->id,
        //     'nim' => '20215003',
        //     'nik' => '',
        //     'nidn' => '',
        //     'gelar_depan' => '',
        //     'gelar_belakang' => '',
        //     'jabatan' => 'Mahasiswa',
            
        // ]);

        // $mahasiswa4 = User::create([
        //     'name' => 'Archy Davin Revel',
        //     'email' => 'archy.20215005@student.atmi.ac.id',
        //     'password' => Hash::make('password'),
        // ]);
        // UserDetail::create([
        //     'user_id' => $mahasiswa4->id,
        //     'nim' => '20215005',
        //     'nik' => '',
        //     'nidn' => '',
        //     'gelar_depan' => '',
        //     'gelar_belakang' => '',
        //     'jabatan' => 'Mahasiswa',
            
        // ]);

        // $mahasiswa5 = User::create([
        //     'name' => 'Ardian Yusak L.S',
        //     'email' => 'ardian.20215006@student.atmi.ac.id',
        //     'password' => Hash::make('password'),
        // ]);
        // UserDetail::create([
        //     'user_id' => $mahasiswa5->id,
        //     'nim' => '20215006',
        //     'nik' => '',
        //     'nidn' => '',
        //     'gelar_depan' => '',
        //     'gelar_belakang' => '',
        //     'jabatan' => 'Mahasiswa',
            
        // ]);

        // $mahasiswa6 = User::create([
        //     'name' => 'Bimo Anggoro Pamungkas',
        //     'email' => 'bimo.20215007@student.atmi.ac.id',
        //     'password' => Hash::make('password'),
        // ]);
        // UserDetail::create([
        //     'user_id' => $mahasiswa6->id,
        //     'nim' => '20215007',
        //     'nik' => '',
        //     'nidn' => '',
        //     'gelar_depan' => '',
        //     'gelar_belakang' => '',
        //     'jabatan' => 'Mahasiswa',
            
        // ]);

        // $mahasiswa7 = User::create([
        //     'name' => 'Cornelia Kartika Dewi',
        //     'email' => 'cornelia.20215009@student.atmi.ac.id',
        //     'password' => Hash::make('password'),
        // ]);
        // UserDetail::create([
        //     'user_id' => $mahasiswa7->id,
        //     'nim' => '20215009',
        //     'nik' => '',
        //     'nidn' => '',
        //     'gelar_depan' => '',
        //     'gelar_belakang' => '',
        //     'jabatan' => 'Mahasiswa',
            
        // ]);

        // $mahasiswa8 = User::create([
        //     'name' => 'Dahayu Shafa Bratarini',
        //     'email' => 'dahayu.20215010@student.atmi.ac.id',
        //     'password' => Hash::make('password'),
        // ]);
        // UserDetail::create([
        //     'user_id' => $mahasiswa8->id,
        //     'nim' => '20215010',
        //     'nik' => '',
        //     'nidn' => '',
        //     'gelar_depan' => '',
        //     'gelar_belakang' => '',
        //     'jabatan' => 'Mahasiswa',
            
        // ]);
        // --------------------------

        // Data Status Dosen---------
        StatusDosen::create([
            'status_dosen' => 'Pembimbing',
        ]); 
       
        StatusDosen::create([
            'status_dosen' => 'Penguji',
        ]); 

         StatusDosen::create([
            'status_dosen' => 'Ketua Penguji',
        ]); 
       
        StatusDosen::create([
            'status_dosen' => 'Anggota Penguji',
        ]); 

        // Data Perusahaan---------
        // DataPerusahaanMagang::create([
        //     'nama' => 'PT ATMI SOLO',
        //     'alamat' => 'Jl. Adisucipto/ Jl. Mojo No. 1,Karangasem, Laweyan, Surakarta 57145 Jawa Tengah, Indonesia',
        // ]); 
        // DataPerusahaanMagang::create([
        //     'nama' => 'PT Djarum',
        //     'alamat' => 'Jl. Jend. Ahmad Yani No.26-28, Krajan, Panjunan, Kec. Kota Kudus, Kabupaten Kudus, Jawa Tengah 59317',
        // ]); 
        
        // DataPerusahaanMagang::create([
        //     'nama' => 'PT. Hartono Istana Teknologi (POLYTRON)',
        //     'alamat' => 'Jl. Kyai H. Raden Asnawi No.126, Gendang Sewu, Bakalankrapyak, Kec. Kaliwungu, Kabupaten Kudus, Jawa Tengah 59332',
        // ]);

        DataAspekPenilaianMagang::create([
            'aspek_penilaian' => 'Catatan Revisi',
            'porsi_penilaian' => '0',
            'deskripsi_penilaian' => 'Catatan Revisi Magang',
            'tipedata' => 'Deskripsi',
        ]);
       
        // --------------------------

        // assign role---------------
        $admin->assignRole($adminrole);
        // $dosen1->assignRole($Dosenrole);
        // $dosen2->assignRole($Dosenrole);
        // $dosen3->assignRole($Dosenrole);
        // $dosen4->assignRole($Dosenrole);


        // $mahasiswa1->assignRole($Mahasiswarole);
        // $mahasiswa2->assignRole($Mahasiswarole);
        // $mahasiswa3->assignRole($Mahasiswarole);
        // $mahasiswa4->assignRole($Mahasiswarole);
        // $mahasiswa5->assignRole($Mahasiswarole);
        // $mahasiswa6->assignRole($Mahasiswarole);
        // $mahasiswa7->assignRole($Mahasiswarole);
        // $mahasiswa8->assignRole($Mahasiswarole);
        // end assign role-----------
        
    }
}
