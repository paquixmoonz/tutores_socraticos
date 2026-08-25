<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectCategoryRequest;
use App\Models\SubjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubjectCategoryController extends Controller
{
    public function index(): View
    {
        $categories = SubjectCategory::withCount('subjects')->orderBy('name')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new SubjectCategory]);
    }

    public function store(SubjectCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = SubjectCategory::uniqueSlug($data['name']);
        SubjectCategory::create($data);

        return redirect()->route('admin.categorias.index')->with('status', 'Categoría creada.');
    }

    public function edit(SubjectCategory $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(SubjectCategoryRequest $request, SubjectCategory $category): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = SubjectCategory::uniqueSlug($data['name'], $category->id);
        $category->update($data);

        return redirect()->route('admin.categorias.index')->with('status', 'Categoría actualizada.');
    }

    public function destroy(SubjectCategory $category): RedirectResponse
    {
        if ($category->subjects()->exists()) {
            return back()->withErrors(['category' => 'No se puede eliminar: hay materias usando esta categoría.']);
        }

        $category->delete();

        return redirect()->route('admin.categorias.index')->with('status', 'Categoría eliminada.');
    }
}
