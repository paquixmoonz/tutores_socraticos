<?php

namespace Database\Seeders;

use App\Models\SubjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Numérica', 'color' => '#0258FC'],
            ['name' => 'Redacción', 'color' => '#C9A227'],
            ['name' => 'Humanística', 'color' => '#6B1F2A'],
            ['name' => 'Metodológica', 'color' => '#52606D'],
            ['name' => 'Programación', 'color' => '#3AAA35'],
            ['name' => 'Ciencias Básicas de Ingeniería', 'color' => '#2E8B2C'],
        ];

        foreach ($categories as $category) {
            SubjectCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                ['name' => $category['name'], 'color' => $category['color']],
            );
        }
    }
}
