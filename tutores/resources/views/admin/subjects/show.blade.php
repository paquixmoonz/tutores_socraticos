@extends('layouts.app')

@section('title', $subject->name)

@section('content')
@include('admin._nav')

<nav aria-label="Ruta de navegación" class="mb-6 text-sm text-unicaes-slate">
    <a href="{{ route('admin.materias.index') }}" class="hover:text-unicaes-green-dark font-medium">Materias</a>
    <span class="mx-1.5">/</span>
    <span class="font-medium text-unicaes-ink">{{ $subject->name }}</span>
</nav>

<div class="mb-8 flex flex-wrap items-start justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ $subject->name }}</h1>
        <p class="mt-1 text-sm text-unicaes-slate">
            @if($subject->code)Código {{ $subject->code }} · @endif
            {{ $subject->category?->name }} ·
            Docente: {{ $subject->teacher?->name ?? 'por asignar' }}
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('catalog.subject', $subject) }}" class="btn-secondary">Ver en catálogo</a>
        <a href="{{ route('admin.materias.edit', $subject) }}" class="btn-primary">Editar</a>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    {{-- Agentes --}}
    <div class="card overflow-hidden">
        <div class="border-b border-unicaes-line bg-unicaes-green/5 px-6 py-4">
            <h2 class="font-semibold text-unicaes-green-dark">Agente socrático</h2>
        </div>
        <div class="p-6">
            @if($subject->agents->isEmpty())
                <p class="mb-5 text-sm text-unicaes-slate">Esta materia aún no tiene agente. Sube el primero.</p>
            @else
                <ul class="mb-6 divide-y divide-unicaes-line">
                    @foreach($subject->agents as $agent)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <div>
                                <p class="text-sm font-medium">{{ $agent->name }} <span class="text-unicaes-slate">· v{{ $agent->version }}</span></p>
                                <p class="text-xs text-unicaes-slate">
                                    {{ $agent->download_count }} descargas ·
                                    {{ $agent->created_at->format('d/m/Y') }} ·
                                    {{ $agent->uploader?->name ?? '—' }}
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                @if($agent->is_active)
                                    <span class="rounded-full bg-unicaes-green/15 px-2.5 py-0.5 text-xs font-semibold text-unicaes-green-dark">Activo</span>
                                @else
                                    <form method="POST" action="{{ route('admin.agents.activate', $agent) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs font-medium text-unicaes-green-dark hover:underline">Activar</button>
                                    </form>
                                @endif
                                <a href="{{ route('agents.download', $agent) }}" class="text-xs font-medium text-unicaes-blue hover:underline">Descargar</a>
                                <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}"
                                      onsubmit="return confirm('¿Eliminar la versión {{ $agent->version }} del agente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('admin.agents.store', $subject) }}" enctype="multipart/form-data" class="space-y-4 border-t border-unicaes-line pt-5">
                @csrf
                <h3 class="text-sm font-semibold">Subir nueva versión</h3>
                <div>
                    <label for="agent-name" class="field-label">Nombre del agente</label>
                    <input id="agent-name" name="name" type="text" required maxlength="150"
                           value="{{ old('name', 'Tutor Socrático de '.$subject->name) }}" class="field-input">
                </div>
                <div>
                    <label for="agent-description" class="field-label">Descripción <span class="font-normal text-unicaes-slate">(opcional)</span></label>
                    <textarea id="agent-description" name="description" rows="2" maxlength="2000" class="field-input">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label for="agent-file" class="field-label">Archivo (txt, md, json o pdf · máx. {{ config('tutores.uploads.agent.max_kb') / 1024 }} MB)</label>
                    <input id="agent-file" name="file" type="file" required accept=".txt,.md,.json,.pdf"
                           class="block w-full text-sm text-unicaes-slate file:mr-3 file:rounded-lg file:border-0 file:bg-unicaes-green/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-unicaes-green-dark hover:file:bg-unicaes-green/20">
                </div>
                <button type="submit" class="btn-primary w-full">Subir agente</button>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Asignación a carreras --}}
        <div class="card overflow-hidden">
            <div class="border-b border-unicaes-line px-6 py-4">
                <h2 class="font-semibold">Carreras y ciclos</h2>
            </div>
            <div class="p-6">
                @if($subject->careers->isEmpty())
                    <p class="mb-5 text-sm text-unicaes-slate">La materia no está asignada a ninguna carrera todavía.</p>
                @else
                    <ul class="mb-6 divide-y divide-unicaes-line">
                        @foreach($subject->careers as $assignedCareer)
                            @php($assignedCycle = $assignedCareer->cycles->firstWhere('id', $assignedCareer->pivot->cycle_id))
                            <li class="flex items-center justify-between gap-3 py-3">
                                <div>
                                    <p class="text-sm font-medium">{{ $assignedCareer->name }}</p>
                                    <p class="text-xs text-unicaes-slate">{{ $assignedCareer->faculty->name }} · {{ $assignedCycle?->label() ?? 'Ciclo eliminado' }}</p>
                                </div>
                                <form method="POST" action="{{ route('admin.subjects.assignments.destroy', [$subject, $assignedCareer->pivot->id]) }}"
                                      onsubmit="return confirm('¿Quitar la materia de {{ $assignedCareer->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Quitar</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('admin.subjects.assignments.store', $subject) }}" class="space-y-4 border-t border-unicaes-line pt-5">
                    @csrf
                    <h3 class="text-sm font-semibold">Asignar a carrera</h3>
                    <div>
                        <label for="career_id" class="field-label">Carrera</label>
                        <select id="career_id" name="career_id" required class="field-input" data-career-select>
                            <option value="" disabled selected>Selecciona una carrera…</option>
                            @foreach($careers as $career)
                                <option value="{{ $career->id }}" data-cycles='@json($career->cycles->map(fn ($c) => ['id' => $c->id, 'label' => $c->label()]))'>
                                    {{ $career->name }} ({{ $career->faculty->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="cycle_id" class="field-label">Ciclo</label>
                        <select id="cycle_id" name="cycle_id" required class="field-input" data-cycle-select>
                            <option value="" disabled selected>Primero elige la carrera…</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary w-full">Asignar</button>
                </form>
            </div>
        </div>

        {{-- Planes de grado --}}
        <div class="card overflow-hidden">
            <div class="border-b border-unicaes-line bg-unicaes-blue/5 px-6 py-4">
                <h2 class="font-semibold text-unicaes-blue">Planes de grado</h2>
            </div>
            <div class="p-6">
                @if($subject->studyPlans->isEmpty())
                    <p class="text-sm text-unicaes-slate">
                        El docente encargado aún no sube el plan de grado.
                    </p>
                @else
                    <ul class="divide-y divide-unicaes-line">
                        @foreach($subject->studyPlans as $plan)
                            <li class="flex items-center justify-between gap-3 py-3">
                                <div>
                                    <p class="text-sm font-medium">{{ $plan->title }} <span class="text-unicaes-slate">· v{{ $plan->version }}</span></p>
                                    <p class="text-xs text-unicaes-slate">{{ $plan->created_at->format('d/m/Y') }} · {{ $plan->uploader?->name ?? '—' }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    @if($plan->is_active)
                                        <span class="rounded-full bg-unicaes-blue/10 px-2.5 py-0.5 text-xs font-semibold text-unicaes-blue">Activo</span>
                                    @endif
                                    <a href="{{ route('study-plans.download', $plan) }}" class="text-xs font-medium text-unicaes-blue hover:underline">Descargar</a>
                                    <form method="POST" action="{{ route('admin.study-plans.destroy', $plan) }}"
                                          onsubmit="return confirm('¿Eliminar esta versión del plan?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Ciclos dependientes de la carrera elegida en el formulario de asignación
    document.querySelectorAll('[data-career-select]').forEach((careerSelect) => {
        careerSelect.addEventListener('change', () => {
            const cycleSelect = document.querySelector('[data-cycle-select]');
            const option = careerSelect.selectedOptions[0];
            const cycles = JSON.parse(option?.dataset.cycles ?? '[]');

            cycleSelect.innerHTML = cycles.length
                ? cycles.map(c => `<option value="${c.id}">${c.label}</option>`).join('')
                : '<option value="" disabled selected>La carrera no tiene ciclos aún</option>';
        });
    });
</script>
@endsection
