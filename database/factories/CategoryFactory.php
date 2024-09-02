<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        $faker = \Faker\Factory::create('id_ID');

        return [
            'name' => $faker->unique()->word(),
            'slug' => function (array $attributes) {
                return Str::slug($attributes['name']);
            },
        ];
    }
}
