<?php

namespace Database\Factories;

use App\Models\Career;
use App\Models\Cycle;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Cycle> */
class CycleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'career_id' => Career::factory(),
            'number' => fake()->unique()->numberBetween(1, 12),
        ];
    }
}
