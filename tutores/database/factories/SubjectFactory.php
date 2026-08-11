<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Subject> */
class SubjectFactory extends Factory
{
    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->words(3, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => strtoupper(Str::random(6)),
            'subject_category_id' => SubjectCategory::factory(),
            'teacher_id' => null,
            'description' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
