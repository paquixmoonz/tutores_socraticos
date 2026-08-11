<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\StudyPlan;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function __construct(private readonly DocumentService $documents) {}

    public function agent(Request $request, Agent $agent): StreamedResponse
    {
        $this->authorize('download', $agent);
        abort_unless(Storage::disk('private')->exists($agent->file_path), 404);

        $agent->increment('download_count');
        $this->documents->recordEvent('agent.downloaded', $agent, $request->user(), [
            'subject' => $agent->subject->name,
            'agent' => $agent->name,
        ]);

        return Storage::disk('private')->download($agent->file_path, $agent->file_name);
    }

    // Contenido en texto plano para "copiar al portapapeles" (solo txt/md/json).
    public function agentContent(Request $request, Agent $agent): JsonResponse
    {
        $this->authorize('download', $agent);
        abort_unless($agent->isCopyable(), 404);
        abort_unless(Storage::disk('private')->exists($agent->file_path), 404);

        $this->documents->recordEvent('agent.copied', $agent, $request->user(), [
            'subject' => $agent->subject->name,
        ]);

        return response()->json([
            'name' => $agent->name,
            'content' => Storage::disk('private')->get($agent->file_path),
        ]);
    }

    public function studyPlan(Request $request, StudyPlan $studyPlan): StreamedResponse
    {
        $this->authorize('download', $studyPlan);
        abort_unless(Storage::disk('private')->exists($studyPlan->file_path), 404);

        $this->documents->recordEvent('study_plan.downloaded', $studyPlan, $request->user(), [
            'subject' => $studyPlan->subject->name,
            'plan' => $studyPlan->title,
        ]);

        return Storage::disk('private')->download($studyPlan->file_path, $studyPlan->file_name);
    }
}
