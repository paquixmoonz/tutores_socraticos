@extends('layouts.app')

@section('title', 'Mis materias')

@section('content')
<div class="mb-8">
    <h1 class="text-xl font-extrabold tracking-tight sm:text-[22px]">Mis materias</h1>
    <p class="mt-1.5 text-[13px] text-unicaes-slate">
        Gestiona el plan de grado de las materias que tienes asignadas.
    </p>
</div>

@if($subjects->isEmpty())
    <div class="card p-10 text-center">
        <p class="text-sm text-unicaes-slate">
            Aún no tienes materias asignadas. El administrador te asignará las materias de las que eres responsable.
        </p>
    </div>
@else
    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($subjects as $subject)
            <li class="stagger-item" style="--i: {{ min($loop->index, 5) }}">
                <a href="{{ route('teacher.subjects.show', $subject) }}"
                   class="card card-interactive group flex h-full flex-col gap-3 px-5 pt-[18px] pb-4">
                    @if($subject->category)
                        <div>
                            <span class="badge"
                                  style="background-color: {{ $subject->category->color ?? '#52606D' }}14; color: {{ $subject->category->color ?? '#52606D' }}">
                                {{ $subject->category->name }}
                            </span>
                        </div>
                    @endif
                    <h2 class="text-sm font-bold leading-snug group-hover:text-unicaes-green-dark">{{ $subject->name }}</h2>
                    @if($subject->code)
                        <p class="font-mono text-[11px] tracking-wider text-unicaes-slate">{{ $subject->code }}</p>
                    @endif
                    <div class="mt-auto border-t border-unicaes-line pt-3">
                        @if($subject->activeStudyPlan)
                            <span class="flex items-center gap-1.5 text-xs font-semibold text-unicaes-green-dark">
                                <svg class="size-2" viewBox="0 0 8 8" aria-hidden="true"><circle cx="4" cy="4" r="3.25" fill="#3AAA35"/></svg>
                                Plan de grado publicado
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-xs font-semibold text-amber-700">
                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                Plan pendiente de publicación
                            </span>
                        @endif
                    </div>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="mt-6">{{ $subjects->links() }}</div>
@endif
@endsection
