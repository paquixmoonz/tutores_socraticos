<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Career;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q'));

        $faculties = Faculty::where('is_active', true)
            ->withCount(['careers' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        // Búsqueda global de materias desde la portada del catálogo.
        $results = null;
        if ($query !== '') {
            $results = Subject::where('is_active', true)
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('code', 'like', "%{$query}%"))
                ->with(['category', 'activeAgent'])
                ->orderBy('name')
                ->limit(30)
                ->get();
        }

        return view('catalog.index', compact('faculties', 'results', 'query'));
    }

    public function faculty(Faculty $faculty): View
    {
        abort_unless($faculty->is_active, 404);

        $careers = $faculty->careers()
            ->where('is_active', true)
            ->withCount('subjects')
            ->orderBy('name')
            ->get();

        return view('catalog.faculty', compact('faculty', 'careers'));
    }

    public function career(Faculty $faculty, Career $career): View
    {
        abort_unless($faculty->is_active && $career->is_active, 404);

        $career->load(['cycles']);

        // Materias de la carrera agrupadas por ciclo (pivote career_subject).
        $subjectsByCycle = $career->subjects()
            ->where('subjects.is_active', true)
            ->with(['category', 'activeAgent', 'activeStudyPlan'])
            ->get()
            ->groupBy('pivot.cycle_id');

        return view('catalog.career', compact('faculty', 'career', 'subjectsByCycle'));
    }

    public function subject(Subject $subject): View
    {
        abort_unless($subject->is_active, 404);

        $subject->load([
            'category', 'teacher', 'activeAgent', 'activeStudyPlan',
            'careers' => fn ($q) => $q->where('careers.is_active', true)->with('faculty'),
        ]);

        $cycles = \App\Models\Cycle::whereIn(
            'id',
            $subject->careers->pluck('pivot.cycle_id')->filter()
        )->get()->keyBy('id');

        return view('catalog.subject', compact('subject', 'cycles'));
    }
}
