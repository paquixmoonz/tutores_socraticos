<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Career;
use App\Models\Cycle;
use App\Models\Faculty;
use App\Models\StudyPlan;
use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::firstOrCreate(
            ['email' => 'docente.demo@catolica.edu.sv'],
            ['name' => 'Docente de Demostración'],
        );
        $teacher->syncRoles(['docente']);

        $student = User::firstOrCreate(
            ['email' => 'estudiante.demo@catolica.edu.sv'],
            ['name' => 'Estudiante de Demostración'],
        );
        $student->syncRoles(['estudiante']);

        $engineering = Faculty::where('slug', 'facultad-de-ingenieria-y-arquitectura')->first();

        $careers = [
            'Ingeniería en Sistemas Informáticos' => ['Programación I', 'Matemática I', 'Redacción Técnica'],
            'Ingeniería Industrial' => ['Matemática I', 'Metodología de la Investigación'],
        ];

        foreach ($careers as $careerName => $subjectNames) {
            $career = Career::firstOrCreate(
                ['faculty_id' => $engineering->id, 'slug' => Str::slug($careerName)],
                ['name' => $careerName, 'plan_year' => '2021-2025'],
            );

            $cycle = Cycle::firstOrCreate(['career_id' => $career->id, 'number' => 1]);

            foreach ($subjectNames as $subjectName) {
                $category = match (true) {
                    str_contains($subjectName, 'Programación') => 'programacion',
                    str_contains($subjectName, 'Matemática') => 'numerica',
                    str_contains($subjectName, 'Redacción') => 'redaccion',
                    default => 'metodologica',
                };

                $subject = Subject::firstOrCreate(
                    ['slug' => Str::slug($subjectName)],
                    [
                        'name' => $subjectName,
                        'code' => strtoupper(Str::of($subjectName)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 3))->join('')),
                        'subject_category_id' => SubjectCategory::where('slug', $category)->value('id'),
                        'teacher_id' => $teacher->id,
                        'description' => "Tutor socrático de {$subjectName}: guía al estudiante mediante preguntas, sin dar respuestas directas, para desarrollar razonamiento propio.",
                    ],
                );

                $career->subjects()->syncWithoutDetaching([$subject->id => ['cycle_id' => $cycle->id]]);

                $this->seedAgent($subject);
                $this->seedStudyPlan($subject, $teacher);
            }
        }
    }

    private function seedAgent(Subject $subject): void
    {
        if ($subject->agents()->exists()) {
            return;
        }

        $content = "# Tutor Socrático — {$subject->name}\n\n"
            ."Eres un tutor socrático de la materia {$subject->name} (UNICAES). "
            ."Nunca des la respuesta directa: guía al estudiante con preguntas graduales, "
            ."pide que justifique cada paso y celebra el razonamiento correcto.\n";

        $path = 'agents/'.$subject->id.'/'.Str::uuid().'.md';
        Storage::disk('private')->put($path, $content);

        Agent::create([
            'subject_id' => $subject->id,
            'uploaded_by' => User::role('admin')->first()?->id,
            'name' => "Tutor Socrático de {$subject->name}",
            'description' => 'Agente de demostración generado por el seeder.',
            'file_path' => $path,
            'file_name' => Str::slug($subject->name).'-tutor.md',
            'file_size' => strlen($content),
            'mime_type' => 'text/markdown',
        ]);
    }

    private function seedStudyPlan(Subject $subject, User $teacher): void
    {
        if ($subject->studyPlans()->exists()) {
            return;
        }

        $content = "%PDF-1.4 demo\nPlan de grado de {$subject->name} (archivo de demostración).";
        $path = 'study-plans/'.$subject->id.'/'.Str::uuid().'.pdf';
        Storage::disk('private')->put($path, $content);

        StudyPlan::create([
            'subject_id' => $subject->id,
            'uploaded_by' => $teacher->id,
            'title' => "Plan de grado — {$subject->name}",
            'file_path' => $path,
            'file_name' => Str::slug($subject->name).'-plan.pdf',
            'file_size' => strlen($content),
            'mime_type' => 'application/pdf',
        ]);
    }
}
