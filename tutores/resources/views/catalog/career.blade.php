@extends('layouts.app')

@section('title', $career->name)

@section('content')
<nav aria-label="Ruta de navegación" class="mb-5 text-xs font-medium text-unicaes-slate">
    <a href="{{ route('catalog.index') }}" class="transition-colors hover:text-unicaes-ink">Catálogo</a>
    <span class="mx-1.5 text-[#CBD2D9]">/</span>
    <a href="{{ route('catalog.faculty', $faculty) }}" class="transition-colors hover:text-unicaes-ink">{{ $faculty->name }}</a>
    <span class="mx-1.5 text-[#CBD2D9]">/</span>
    <span class="font-semibold text-unicaes-ink">{{ $career->name }}</span>
</nav>

<div class="mb-10">
    <h1 class="text-xl font-extrabold tracking-tight sm:text-[22px]">{{ $career->name }}</h1>
    <p class="mt-1 text-[13px] text-unicaes-slate">
        {{ $faculty->name }}@if($career->plan_year) · Plan {{ $career->plan_year }}@endif
    </p>
</div>

@if($career->cycles->isEmpty())
    <div class="card p-10 text-center text-sm text-unicaes-slate">
        Esta carrera aún no tiene ciclos ni materias registradas.
    </div>
@else
    <div class="space-y-10">
        @foreach($career->cycles as $cycle)
            @php($subjects = $subjectsByCycle->get($cycle->id, collect()))
            <section aria-label="{{ $cycle->label() }}">
                <h2 class="mb-4 flex items-center gap-2.5 text-sm font-bold">
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-[7px] bg-unicaes-green/15 text-[11px] font-extrabold text-unicaes-green-dark">{{ $cycle->roman() }}</span>
                    {{ $cycle->label() }}
                    <span class="text-xs font-normal text-unicaes-slate">· {{ $subjects->count() }} {{ Str::plural('materia', $subjects->count()) }}</span>
                </h2>

                @if($subjects->isEmpty())
                    <p class="rounded-[14px] border border-dashed border-unicaes-line px-4 py-5 text-center text-sm text-unicaes-slate">
                        Sin materias en este ciclo todavía.
                    </p>
                @else
                    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($subjects as $subject)
                            <li class="stagger-item" style="--i: {{ min($loop->index, 5) }}"><x-subject-card :subject="$subject" /></li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>
@endif
@endsection
