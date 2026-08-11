<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectRequest;
use App\Models\Career;
use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function __construct(private readonly DocumentService $documents) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $categoryId = $request->integer('categoria');

        $subjects = Subject::with(['category', 'teacher', 'activeAgent', 'activeStudyPlan'])
            ->when($search !== '', fn ($q) => $q->where(fn ($sub) => $sub
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")))
            ->when($categoryId, fn ($q) => $q->where('subject_category_id', $categoryId))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = SubjectCategory::orderBy('name')->get();

        return view('admin.subjects.index', compact('subjects', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        return view('admin.subjects.form', [
            'subject' => new Subject,
            'categories' => SubjectCategory::orderBy('name')->get(),
            'teachers' => $this->teachers(),
        ]);
    }

    public function store(SubjectRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name']);
        $subject = Subject::create($data);

        return redirect()->route('admin.materias.show', $subject)
            ->with('status', 'Materia creada. Asígnala a carreras y sube su agente.');
    }

    public function show(Subject $subject): View
    {
        $subject->load([
            'category', 'teacher',
            'agents.uploader', 'studyPlans.uploader',
            'careers.faculty', 'careers.cycles',
        ]);

        return view('admin.subjects.show', [
            'subject' => $subject,
            'careers' => Career::with(['faculty', 'cycles'])->orderBy('name')->get(),
        ]);
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.form', [
            'subject' => $subject,
            'categories' => SubjectCategory::orderBy('name')->get(),
            'teachers' => $this->teachers(),
        ]);
    }

    public function update(SubjectRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        if ($data['name'] !== $subject->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $subject->id);
        }
        $subject->update($data);

        return redirect()->route('admin.materias.show', $subject)->with('status', 'Materia actualizada.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        foreach ($subject->agents as $agent) {
            $this->documents->deleteDocumentFile($agent);
        }
        foreach ($subject->studyPlans as $plan) {
            $this->documents->deleteDocumentFile($plan);
        }

        $subject->delete();

        return redirect()->route('admin.materias.index')->with('status', 'Materia eliminada junto con sus documentos.');
    }

    private function teachers()
    {
        return User::role(['docente', 'admin'])->orderBy('name')->get();
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;
        while (Subject::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
