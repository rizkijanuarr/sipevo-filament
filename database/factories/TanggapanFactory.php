<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Pengaduan;
use App\Models\Tanggapan;
use App\Models\User;

class TanggapanFactory extends Factory
{
    protected $model = Tanggapan::class;

    public function definition(): array
    {
        Tanggapan::unsetEventDispatcher();
        $faker = \Faker\Factory::create('id_ID');

        return [
            'pengaduan_id' => rand(1, 1000),
            'comment' => $faker->text(),
        ];
    }
}
