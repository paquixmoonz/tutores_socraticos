<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyPlan;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;

class StudyPlanController extends Controller
{
    public function __construct(private readonly DocumentService $documents) {}

    public function destroy(StudyPlan $studyPlan): RedirectResponse
    {
        $this->documents->deleteDocumentFile($studyPlan);
        $studyPlan->delete();

        return back()->with('status', 'Plan de grado eliminado.');
    }
}
