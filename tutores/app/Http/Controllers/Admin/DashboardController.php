<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Career;
use App\Models\Faculty;
use App\Models\StudyPlan;
use App\Models\Subject;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'faculties' => Faculty::count(),
            'careers' => Career::count(),
            'subjects' => Subject::count(),
            'users' => User::count(),
            'agents' => Agent::where('is_active', true)->count(),
            'plans' => StudyPlan::where('is_active', true)->count(),
            'downloads' => Agent::sum('download_count'),
            'subjects_without_agent' => Subject::where('is_active', true)
                ->whereDoesntHave('agents', fn ($q) => $q->where('is_active', true))->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
