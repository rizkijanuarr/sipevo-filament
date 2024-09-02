<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Pengaduan;
use App\Models\Tanggapan;
use App\Models\User;

class TanggapanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tanggapan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'pengaduan_id' => Pengaduan::factory(),
            'user_id' => User::factory(),
            'comment' => $this->faker->text(),
            'image' => $this->faker->word(),
        ];
    }
}
