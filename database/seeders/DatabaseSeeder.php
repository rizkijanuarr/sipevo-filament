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
        \App\Models\Category::factory(10)->create();
        \App\Models\Pengaduan::factory(100)->create();
        \App\Models\Tanggapan::factory(100)->create();
    }
}
