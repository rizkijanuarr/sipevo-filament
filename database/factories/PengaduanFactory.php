<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Pengaduan;
use App\Models\User;

class PengaduanFactory extends Factory
{
    protected $model = Pengaduan::class;

    public function definition(): array
    {
        Pengaduan::unsetEventDispatcher();
        $faker = \Faker\Factory::create('id_ID');

        return [
            'category_id' => rand(1, 5),
            'title' => $faker->sentence(),
            'description' => $faker->paragraph(),
            'location' => $faker->address(),
            'status' => collect(\App\Enums\PengaduanStatus::cases())->random(),
        ];
    }
}
