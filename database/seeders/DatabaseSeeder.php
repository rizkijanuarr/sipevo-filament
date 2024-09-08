<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,
            UserSeeder::class,
        ]);
        \App\Models\Category::factory(5)->create();
        \App\Models\Pengaduan::factory(1000)->create();
        \App\Models\Tanggapan::factory(1000)->create();

        \Laravel\Prompts\info('Seeding completed bro!');
    }
}
