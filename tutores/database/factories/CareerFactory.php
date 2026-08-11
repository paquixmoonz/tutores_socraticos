<?php

namespace Database\Factories;

use App\Models\Career;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Career> */
class CareerFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Ingeniería en '.fake()->unique()->words(2, true);

        return [
            'faculty_id' => Faculty::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'plan_year' => '2021-2025',
            'is_active' => true,
        ];
    }
}
