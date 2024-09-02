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

        $name = $faker->unique()->word();
        $slug = Str::slug($name);

        // Memastikan slug unik dengan loop jika perlu
        while (Category::where('slug', $slug)->exists()) {
            $name = $faker->unique()->word();
            $slug = Str::slug($name);
        }

        return [
            'name' => $name,
            'slug' => $slug,
        ];
    }
}
