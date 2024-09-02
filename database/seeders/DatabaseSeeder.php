<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
        \App\Models\Category::factory(5)->create();
        \App\Models\Pengaduan::factory(1000)->create();
        \App\Models\Tanggapan::factory(1000)->create();
    }
}
