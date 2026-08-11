<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectAssignmentRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class SubjectAssignmentController extends Controller
{
    public function store(SubjectAssignmentRequest $request, Subject $subject): RedirectResponse
    {
        $careerId = (int) $request->validated('career_id');
        $cycleId = (int) $request->validated('cycle_id');

        $exists = DB::table('career_subject')
            ->where(['career_id' => $careerId, 'cycle_id' => $cycleId, 'subject_id' => $subject->id])
            ->exists();

        if ($exists) {
            return back()->withErrors(['career_id' => 'La materia ya está asignada a esa carrera y ciclo.']);
        }

        $subject->careers()->attach($careerId, ['cycle_id' => $cycleId]);

        return back()->with('status', 'Materia asignada a la carrera.');
    }

    public function destroy(Subject $subject, int $assignment): RedirectResponse
    {
        DB::table('career_subject')
            ->where('id', $assignment)
            ->where('subject_id', $subject->id)
            ->delete();

        return back()->with('status', 'Asignación eliminada.');
    }
}
