<?php

namespace Database\Factories;

use App\Models\StudyPlan;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<StudyPlan> */
class StudyPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'uploaded_by' => null,
            'title' => 'Plan de grado '.fake()->words(2, true),
            'file_path' => 'study-plans/test/'.Str::uuid().'.pdf',
            'file_name' => 'plan.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'version' => 1,
            'is_active' => true,
        ];
    }
}
