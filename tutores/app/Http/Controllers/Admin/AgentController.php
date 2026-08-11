<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgentRequest;
use App\Models\Agent;
use App\Models\Subject;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;

class AgentController extends Controller
{
    public function __construct(private readonly DocumentService $documents) {}

    public function store(StoreAgentRequest $request, Subject $subject): RedirectResponse
    {
        $this->documents->storeAgent(
            $subject,
            $request->file('file'),
            $request->user(),
            $request->validated('name'),
            $request->validated('description'),
        );

        return back()->with('status', 'Agente subido correctamente. Es la versión activa.');
    }

    public function activate(Agent $agent): RedirectResponse
    {
        $agent->subject->agents()->update(['is_active' => false]);
        $agent->update(['is_active' => true]);

        return back()->with('status', "La versión {$agent->version} del agente ahora es la activa.");
    }

    public function destroy(Agent $agent): RedirectResponse
    {
        $this->documents->deleteDocumentFile($agent);
        $agent->delete();

        return back()->with('status', 'Versión del agente eliminada.');
    }
}
