<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CycleRequest;
use App\Models\Career;
use App\Models\Cycle;
use Illuminate\Http\RedirectResponse;

class CycleController extends Controller
{
    public function store(CycleRequest $request, Career $career): RedirectResponse
    {
        $career->cycles()->create($request->validated());

        return back()->with('status', 'Ciclo agregado.');
    }

    public function destroy(Cycle $cycle): RedirectResponse
    {
        $inUse = $cycle->career->subjects()->wherePivot('cycle_id', $cycle->id)->exists();
        if ($inUse) {
            return back()->withErrors(['cycle' => 'No se puede eliminar: el ciclo tiene materias asignadas.']);
        }

        $cycle->delete();

        return back()->with('status', 'Ciclo eliminado.');
    }
}
