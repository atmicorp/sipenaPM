<?php

return [
    // Sakelar login manual. Standarnya MATI.
    'manual_aktif' => env('MANUAL_LOGIN_ENABLED', false),

    // Alamat rahasia, contoh: masuk-admin-x7k2q9
    'manual_path' => env('MANUAL_LOGIN_PATH'),

    // Opsional: batasi ke IP tertentu, pisahkan dengan koma. Kosong = semua IP boleh.
    'ip_diizinkan' => array_values(array_filter(array_map('trim', explode(',', (string) env('MANUAL_LOGIN_IPS', ''))))),

    // Maksimal percobaan gagal per menit, per kombinasi email + IP
    'maks_percobaan' => 5,
];