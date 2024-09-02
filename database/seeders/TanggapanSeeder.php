<?php

namespace Database\Seeders;

use App\Models\Tanggapan;
use Illuminate\Database\Seeder;

class TanggapanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tanggapan::factory()->count(5)->create();
    }
}
