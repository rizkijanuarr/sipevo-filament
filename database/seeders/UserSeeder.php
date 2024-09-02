<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // USER 1
        \App\Models\User::create([
            'name' => 'Mas Ikyy',
            'email'  => 'masikyy@id.rizkijanuar.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // USER 2
        \App\Models\User::create([
            'name' => 'Aan Citra',
            'email' => 'aancitra@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
