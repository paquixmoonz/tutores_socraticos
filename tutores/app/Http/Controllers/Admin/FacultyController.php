<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function index(): View
    {
        $faculties = Faculty::withCount('careers')->orderBy('name')->paginate(15);

        return view('admin.faculties.index', compact('faculties'));
    }

    public function create(): View
    {
        return view('admin.faculties.form', ['faculty' => new Faculty]);
    }

    public function store(FacultyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Faculty::uniqueSlug($data['name']);
        Faculty::create($data);

        return redirect()->route('admin.facultades.index')->with('status', 'Facultad creada.');
    }

    public function edit(Faculty $faculty): View
    {
        return view('admin.faculties.form', compact('faculty'));
    }

    public function update(FacultyRequest $request, Faculty $faculty): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        if ($data['name'] !== $faculty->name) {
            $data['slug'] = Faculty::uniqueSlug($data['name'], $faculty->id);
        }
        $faculty->update($data);

        return redirect()->route('admin.facultades.index')->with('status', 'Facultad actualizada.');
    }

    public function destroy(Faculty $faculty): RedirectResponse
    {
        if ($faculty->careers()->exists()) {
            return back()->withErrors(['faculty' => 'No se puede eliminar: la facultad tiene carreras asociadas.']);
        }

        $faculty->delete();

        return redirect()->route('admin.facultades.index')->with('status', 'Facultad eliminada.');
    }
}
