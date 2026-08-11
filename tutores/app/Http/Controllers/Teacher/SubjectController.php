<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $subjects = Subject::query()
            ->when(! $user->isAdmin(), fn ($q) => $q->where('teacher_id', $user->id))
            ->with(['category', 'activeStudyPlan', 'activeAgent'])
            ->orderBy('name')
            ->paginate(12);

        return view('teacher.subjects.index', compact('subjects'));
    }

    public function show(Request $request, Subject $subject): View
    {
        $this->authorize('managePlans', $subject);

        $subject->load(['category', 'studyPlans.uploader', 'careers.faculty']);

        return view('teacher.subjects.show', compact('subject'));
    }
}
