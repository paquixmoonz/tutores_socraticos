<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudyPlanRequest;
use App\Models\StudyPlan;
use App\Models\Subject;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudyPlanController extends Controller
{
    public function __construct(private readonly DocumentService $documents) {}

    public function store(StoreStudyPlanRequest $request, Subject $subject): RedirectResponse
    {
        $this->documents->storeStudyPlan(
            $subject,
            $request->file('file'),
            $request->user(),
            $request->validated('title'),
        );

        return back()->with('status', 'Plan de grado subido correctamente. Es la versión activa.');
    }

    public function activate(Request $request, StudyPlan $studyPlan): RedirectResponse
    {
        $this->authorize('manage', $studyPlan);

        $studyPlan->subject->studyPlans()->update(['is_active' => false]);
        $studyPlan->update(['is_active' => true]);

        return back()->with('status', "La versión {$studyPlan->version} ahora es la activa.");
    }

    public function destroy(Request $request, StudyPlan $studyPlan): RedirectResponse
    {
        $this->authorize('manage', $studyPlan);

        $this->documents->deleteDocumentFile($studyPlan);
        $studyPlan->delete();

        return back()->with('status', 'Versión del plan eliminada.');
    }
}
