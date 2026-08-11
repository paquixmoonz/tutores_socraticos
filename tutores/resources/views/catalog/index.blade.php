@extends('layouts.app')

@section('title', 'Catálogo')

@section('content')
<div class="animate-slide-in-up mb-12 max-w-[600px]">
    <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Catálogo de tutores socráticos</h1>
    <p class="mt-2.5 text-sm leading-relaxed text-unicaes-slate sm:text-[15px]">
        Busca tu materia, descarga el agente y pégalo como primer mensaje en ChatGPT, Claude o Gemini.
    </p>
</div>

<form method="GET" action="{{ route('catalog.index') }}" role="search"
      class="animate-slide-in-up relative mb-12 max-w-[560px] [animation-delay:60ms]">
    <label for="q" class="sr-only">Buscar materia</label>
    <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-unicaes-slate" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/>
    </svg>
    <input id="q" type="search" name="q" value="{{ $query }}" placeholder="Busca una materia por nombre o código…"
           class="w-full rounded-xl border border-unicaes-line bg-white py-3 pl-11 pr-4 text-[15px] text-unicaes-ink placeholder-unicaes-slate/60 shadow-[0_1px_4px_rgba(0,0,0,0.04)] transition-all focus:border-unicaes-green focus:shadow-[0_0_0_3px_rgba(58,170,53,0.09)] focus:outline-none">
    <button type="submit" class="sr-only">Buscar</button>
</form>

@if(!is_null($results))
    <section aria-label="Resultados de búsqueda" class="mb-13">
        <p class="mb-4 text-[13px] text-unicaes-slate">
            {{ $results->count() }} {{ Str::plural('resultado', $results->count()) }} para
            <strong class="font-semibold text-unicaes-ink">“{{ $query }}”</strong>
        </p>

        @if($results->isEmpty())
            <div class="rounded-[14px] border border-dashed border-unicaes-line px-6 py-8 text-center text-sm text-unicaes-slate">
                No se encontraron materias. Intenta otro término o navega por facultad.
            </div>
        @else
            <ul class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($results as $subject)
                    <li class="stagger-item" style="--i: {{ min($loop->index, 5) }}"><x-subject-card :subject="$subject" /></li>
                @endforeach
            </ul>
        @endif
    </section>
@endif

<section aria-label="Facultades">
    <div class="mb-6">
        <h2 class="text-lg font-extrabold tracking-tight">Facultades</h2>
        <p class="mt-1 text-[13px] text-unicaes-slate">Navega por facultad para encontrar materias por carrera y ciclo.</p>
    </div>

    @if($faculties->isEmpty())
        <div class="card p-10 text-center text-sm text-unicaes-slate">
            Aún no hay facultades registradas.
        </div>
    @else
        <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($faculties as $faculty)
                @php($initials = collect(explode(' ', str_ireplace(['Facultad de ', 'Facultad '], '', $faculty->name)))
                    ->reject(fn ($w) => in_array(mb_strtolower($w), ['de', 'del', 'la', 'las', 'los', 'y', 'e', 'en']))
                    ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode(''))
                <li class="stagger-item" style="--i: {{ min($loop->index, 5) }}">
                    <a href="{{ route('catalog.faculty', $faculty) }}"
                       class="card card-interactive group flex h-full items-center gap-4 px-5 py-[18px]">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-[10px] bg-unicaes-maroon/10 text-xs font-extrabold tracking-wide text-unicaes-maroon">
                            {{ $initials }}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[13px] font-semibold leading-snug group-hover:text-unicaes-green-dark">{{ $faculty->name }}</span>
                            <span class="mt-0.5 block text-[11px] text-unicaes-slate">
                                {{ $faculty->careers_count }} {{ Str::plural('carrera', $faculty->careers_count) }}
                            </span>
                        </span>
                        <svg class="size-3.5 shrink-0 text-unicaes-line transition-colors group-hover:text-unicaes-slate" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
