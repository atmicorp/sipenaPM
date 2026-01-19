<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            DataSampelSeeder::class,
            KategoriTASeeder::class,
            AspekPenilaianTASeeder::class,
        ]);
    }

    // nanti saat TA berlansung baru dilanjut seeder custom seeder
}
