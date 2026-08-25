<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CareerRequest;
use App\Models\Career;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(Request $request): View
    {
        $facultyId = $request->integer('faculty');

        $careers = Career::with('faculty')
            ->withCount(['cycles', 'subjects'])
            ->when($facultyId, fn ($q) => $q->where('faculty_id', $facultyId))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $faculties = Faculty::orderBy('name')->get();

        return view('admin.careers.index', compact('careers', 'faculties', 'facultyId'));
    }

    public function create(): View
    {
        return view('admin.careers.form', [
            'career' => new Career,
            'faculties' => Faculty::orderBy('name')->get(),
        ]);
    }

    public function store(CareerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Career::uniqueSlug($data['name'], scope: ['faculty_id' => $data['faculty_id']]);
        $career = Career::create($data);

        return redirect()->route('admin.carreras.show', $career)->with('status', 'Carrera creada. Ahora agrega sus ciclos.');
    }

    public function show(Career $career): View
    {
        $career->load(['faculty', 'cycles']);

        $subjectsByCycle = $career->subjects()->with('category')->get()->groupBy('pivot.cycle_id');

        return view('admin.careers.show', compact('career', 'subjectsByCycle'));
    }

    public function edit(Career $career): View
    {
        return view('admin.careers.form', [
            'career' => $career,
            'faculties' => Faculty::orderBy('name')->get(),
        ]);
    }

    public function update(CareerRequest $request, Career $career): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        if ($data['name'] !== $career->name || (int) $data['faculty_id'] !== (int) $career->faculty_id) {
            $data['slug'] = Career::uniqueSlug($data['name'], $career->id, ['faculty_id' => $data['faculty_id']]);
        }
        $career->update($data);

        return redirect()->route('admin.carreras.index')->with('status', 'Carrera actualizada.');
    }

    public function destroy(Career $career): RedirectResponse
    {
        if ($career->subjects()->exists()) {
            return back()->withErrors(['career' => 'No se puede eliminar: la carrera tiene materias asociadas.']);
        }

        $career->delete();

        return redirect()->route('admin.carreras.index')->with('status', 'Carrera eliminada.');
    }
}
