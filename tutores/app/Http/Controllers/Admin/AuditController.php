<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'usuario' => trim((string) $request->query('usuario')),
            'evento' => trim((string) $request->query('evento')),
            'modelo' => trim((string) $request->query('modelo')),
            'desde' => $request->query('desde'),
            'hasta' => $request->query('hasta'),
        ];

        $audits = Audit::query()
            ->with('user')
            ->when($filters['usuario'] !== '', function ($q) use ($filters) {
                $q->whereIn('user_id', User::where('email', 'like', "%{$filters['usuario']}%")
                    ->orWhere('name', 'like', "%{$filters['usuario']}%")
                    ->pluck('id'));
            })
            ->when($filters['evento'] !== '', fn ($q) => $q->where('event', $filters['evento']))
            ->when($filters['modelo'] !== '', fn ($q) => $q->where('auditable_type', 'like', "%{$filters['modelo']}%"))
            ->when($filters['desde'], fn ($q) => $q->whereDate('created_at', '>=', $filters['desde']))
            ->when($filters['hasta'], fn ($q) => $q->whereDate('created_at', '<=', $filters['hasta']))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $events = Audit::select('event')->distinct()->orderBy('event')->pluck('event');

        return view('admin.audits.index', compact('audits', 'filters', 'events'));
    }
}
