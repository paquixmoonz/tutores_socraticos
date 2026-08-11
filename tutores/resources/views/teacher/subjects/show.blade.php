@extends('layouts.app')

@section('title', $subject->name)

@section('content')
<nav aria-label="Ruta de navegación" class="mb-5 text-xs font-medium text-unicaes-slate">
    <a href="{{ route('teacher.subjects.index') }}" class="transition-colors hover:text-unicaes-ink">Mis materias</a>
    <span class="mx-1.5 text-[#CBD2D9]">/</span>
    <span class="font-semibold text-unicaes-ink">{{ $subject->name }}</span>
</nav>

<div class="mb-7 flex flex-wrap items-start justify-between gap-3">
    <div>
        <h1 class="text-xl font-extrabold tracking-tight sm:text-[22px]">{{ $subject->name }}</h1>
        <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px] text-unicaes-slate">
            @if($subject->code)<span class="font-mono tracking-wide">{{ $subject->code }}</span>@endif
            @if($subject->code && $subject->category)<span aria-hidden="true">·</span>@endif
            {{ $subject->category?->name }}
        </p>
    </div>
    <a href="{{ route('catalog.subject', $subject) }}"
       class="flex items-center gap-1.5 rounded-lg border border-unicaes-line px-3 py-1.5 text-xs font-semibold text-unicaes-blue transition-colors hover:bg-white">
        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        Ver como estudiante
    </a>
</div>

<div class="grid items-start gap-5 lg:grid-cols-[1fr_300px]">
    <div class="card overflow-hidden">
        <div class="border-b border-unicaes-line px-[22px] py-[18px]">
            <h2 class="text-[13px] font-bold tracking-wider uppercase">Historial de versiones</h2>
        </div>

        @if($subject->studyPlans->isEmpty())
            <p class="p-8 text-center text-sm text-unicaes-slate">
                Aún no has subido el plan de grado de esta materia. Usa el formulario para publicar la primera versión.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[540px]">
                    <thead class="bg-unicaes-mist">
                        <tr class="border-b border-unicaes-line">
                            <th class="px-[22px] py-2.5 text-left text-[10px] font-bold tracking-wide uppercase text-unicaes-slate">Título</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold tracking-wide uppercase text-unicaes-slate">Estado</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold tracking-wide uppercase text-unicaes-slate">Fecha</th>
                            <th class="px-4 py-2.5"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subject->studyPlans as $plan)
                            <tr class="border-b border-unicaes-mist {{ $plan->is_active ? 'bg-unicaes-green/[0.03]' : '' }}">
                                <td class="px-[22px] py-3.5">
                                    <p class="text-[13px] font-semibold">{{ $plan->title }}</p>
                                    <p class="mt-0.5 font-mono text-[11px] text-unicaes-slate">v{{ $plan->version }} · {{ Number::fileSize($plan->file_size) }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($plan->is_active)
                                        <span class="badge bg-unicaes-green/10 text-unicaes-green-dark">Activa</span>
                                    @else
                                        <span class="badge bg-unicaes-line text-unicaes-slate">Histórica</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs text-unicaes-slate">{{ $plan->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex justify-end gap-2.5 whitespace-nowrap">
                                        <a href="{{ route('study-plans.download', $plan) }}" class="text-xs font-semibold text-unicaes-blue hover:underline">Descargar</a>
                                        @unless($plan->is_active)
                                            <form method="POST" action="{{ route('teacher.study-plans.activate', $plan) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-unicaes-green-dark hover:underline">Activar</button>
                                            </form>
                                            <form method="POST" action="{{ route('teacher.study-plans.destroy', $plan) }}"
                                                  onsubmit="return confirm('¿Eliminar esta versión del plan? Esta acción no se puede deshacer.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Eliminar</button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card overflow-hidden">
        <div class="border-b border-unicaes-line px-5 py-[18px]">
            <h2 class="text-[13px] font-bold tracking-wider uppercase">Subir nueva versión</h2>
        </div>
        <form method="POST" action="{{ route('teacher.study-plans.store', $subject) }}" enctype="multipart/form-data" class="space-y-3.5 px-5 pt-[18px] pb-5">
            @csrf
            <div>
                <label for="title" class="field-label">Título del plan</label>
                <input id="title" name="title" type="text" required maxlength="150"
                       value="{{ old('title', 'Plan de Grado '.now()->year) }}" class="field-input">
            </div>
            <div>
                <span class="field-label">Archivo</span>
                <label data-dropzone for="file"
                       class="flex cursor-pointer flex-col items-center gap-1.5 rounded-[10px] border-[1.5px] border-dashed border-unicaes-line px-4 py-5 text-center transition-colors hover:border-unicaes-green hover:bg-unicaes-green/[0.02]">
                    <svg class="size-4 text-unicaes-slate" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16 16-4-4-4 4"/><line stroke-linecap="round" x1="12" y1="12" x2="12" y2="21"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                    <span data-dropzone-text class="text-xs text-unicaes-slate">
                        PDF o DOCX — máx. {{ config('tutores.uploads.study_plan.max_kb') / 1024 }} MB
                    </span>
                </label>
                <input id="file" name="file" type="file" required accept=".pdf,.docx" class="sr-only">
            </div>
            <p class="text-[11px] leading-relaxed text-unicaes-slate">La nueva versión pasará a ser la activa automáticamente.</p>
            <button type="submit" class="btn-primary w-full">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16 16-4-4-4 4"/><line stroke-linecap="round" x1="12" y1="12" x2="12" y2="21"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                Publicar plan
            </button>
        </form>
    </div>
</div>
@endsection
