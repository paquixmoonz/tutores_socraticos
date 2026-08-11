<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Agent> */
class AgentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'uploaded_by' => null,
            'name' => 'Tutor Socrático '.fake()->words(2, true),
            'description' => fake()->sentence(),
            'file_path' => 'agents/test/'.Str::uuid().'.md',
            'file_name' => 'tutor.md',
            'file_size' => 512,
            'mime_type' => 'text/markdown',
            'version' => 1,
            'download_count' => 0,
            'is_active' => true,
        ];
    }
}
