<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // USER 1
        \App\Models\User::create([
            'name' => 'Rizki',
            'email'  => 'rizki@rizkijanuar.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // USER 2
        \App\Models\User::create([
            'name' => 'Citra',
            'email' => 'citra@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
