<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            'Facultad de Ingeniería y Arquitectura',
            'Facultad de Ciencias Empresariales',
            'Facultad de Ciencias y Humanidades',
            'Facultad de Ciencias de la Salud',
            'Facultad Multidisciplinaria de Ilobasco',
        ];

        foreach ($faculties as $name) {
            Faculty::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
