<?php

namespace Database\Factories;

use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SubjectCategory> */
class SubjectCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'color' => '#3AAA35',
        ];
    }
}
