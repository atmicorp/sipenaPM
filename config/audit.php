<?php

return [

    // Kunci yang isinya TIDAK PERNAH boleh masuk log (tidak peka huruf besar/kecil).
    // Nilainya diganti "[DISEMBUNYIKAN]", jadi tetap terlihat bahwa kunci itu ada.
    'kunci_terlarang' => [
        'password',
        'new_password',
        'confirm_password',
        'password_confirmation',
        'current_password',
        'remember_token',
        '_token',
        'photo',
      
    ],

    // Teks lebih panjang dari ini dipotong (pengaman kalau ada base64 yang lolos).
    'maks_panjang_teks' => 200,

    // Lama penyimpanan log (dipakai di Fase 3).
    'retensi_bulan' => 12,

    // Route GET yang sebenarnya menghapus/mereset data, jadi tetap dicatat.
    'get_destruktif' => [
        'deletedatamagang',
        'pesertata.destroy',
        'pengujita.destroy',
        'resetpenilaianmagang',
    ],

    // Peta nama route => [jenis, aksi, deskripsi]
    'rute' => [

        // ---- KEAMANAN ----
        'uploadphoto'          => ['keamanan', 'ganti_foto',            'Ganti foto profil sendiri'],
        'uploadphotoeditadmin' => ['keamanan', 'ganti_foto_oleh_admin', 'Admin mengganti foto user lain'],
        'change.password'      => ['keamanan', 'ganti_password',        'Admin mengganti password user lain'],
        'update.user'          => ['keamanan', 'ubah_user',             'Admin mengubah data user'],
        'storeusermanual'      => ['keamanan', 'tambah_user',           'Admin menambah user manual'],

        // ---- DATA: magang ----
        'storepenempatanmagang'          => ['data', 'tambah_penempatan_magang', 'Tambah penempatan praktik kerja'],
        'storeperusahaanmagang'          => ['data', 'tambah_perusahaan_magang', 'Tambah perusahaan magang'],
        'pesertaupdate'                  => ['data', 'ubah_peserta_magang',      'Ubah data peserta magang'],
        'storeaspekdata'                 => ['data', 'tambah_aspek_magang',      'Tambah aspek penilaian magang'],
        'storedatapembimbing'            => ['data', 'tambah_pembimbing_magang', 'Tambah data pembimbing magang'],
        'deletedatamagang'               => ['data', 'hapus_data_magang',        'Hapus data magang'],
        'deleteAspek'                    => ['data', 'hapus_aspek_magang',       'Hapus aspek penilaian magang'],
        'resetpenilaianmagang'           => ['data', 'reset_penilaian_magang',   'Reset penilaian magang'],
        'deletepenilaianmagang'          => ['data', 'hapus_penilaian_magang',   'Hapus penilaian magang'],
        'importpesertamagang.preview'    => ['data', 'preview_import_magang',    'Preview import peserta magang'],
        'importpesertamagang.store'      => ['data', 'import_peserta_magang',    'Simpan import peserta magang'],
        'importpesertamagang.cancel'     => ['data', 'batal_import_magang',      'Batalkan import peserta magang'],

        // ---- DATA: tugas akhir ----
        'pesertata.destroy'    => ['data', 'hapus_peserta_ta',   'Hapus peserta TA'],
        'pesertata.update'     => ['data', 'ubah_peserta_ta',    'Ubah peserta TA'],
        'dosenta.update'       => ['data', 'ubah_penguji_ta',    'Ubah dosen penguji TA'],
        'pengujita.destroy'    => ['data', 'hapus_penguji_ta',   'Hapus penguji TA'],
        'kategorita.store'     => ['data', 'tambah_kategori_ta', 'Tambah kategori sidang TA'],
        'kategorita.update'    => ['data', 'ubah_kategori_ta',   'Ubah kategori sidang TA'],
        'kategorita.destroy'   => ['data', 'hapus_kategori_ta',  'Hapus kategori sidang TA'],
        'kelompokta.update'    => ['data', 'ubah_kelompok_ta',   'Ubah kelompok TA'],
        'kta.update'           => ['data', 'ubah_kelompok_ta',   'Edit kelompok TA'],
        'kelompokta.store'     => ['data', 'tambah_kelompok_ta', 'Tambah kelompok TA'],
        'updatejadwalta'       => ['data', 'ubah_jadwal_ta',     'Ubah jadwal TA'],
        'storeaspekdatata'           => ['data', 'tambah_aspek_ta',          'Tambah aspek penilaian TA'],
        'deleteaspekta'              => ['data', 'hapus_aspek_ta',           'Hapus aspek penilaian TA'],
        'storeaspekdatataindividu'   => ['data', 'tambah_aspek_ta_individu', 'Tambah aspek penilaian TA individu'],
        'deleteaspektaindividu'      => ['data', 'hapus_aspek_ta_individu',  'Hapus aspek penilaian TA individu'],

        // ---- DATA: reset ----
        'reset.database'       => ['data', 'reset_database', 'Reset database'],

        // ---- NILAI (nilai lama/baru menyusul di Fase 3) ----
        'updateNilaiKelompok'  => ['nilai', 'edit_nilai_ta_kelompok', 'Admin mengedit nilai TA kelompok'],
        'updateNilaiIndividu'  => ['nilai', 'edit_nilai_ta_individu', 'Admin mengedit nilai TA individu'],
        'updateNilaimagang'    => ['nilai', 'edit_nilai_magang',      'Admin mengedit nilai magang'],
    ],
];