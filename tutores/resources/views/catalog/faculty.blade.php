@extends('layouts.app')

@section('title', $faculty->name)

@section('content')
<nav aria-label="Ruta de navegación" class="mb-5 text-xs font-medium text-unicaes-slate">
    <a href="{{ route('catalog.index') }}" class="transition-colors hover:text-unicaes-ink">Catálogo</a>
    <span class="mx-1.5 text-[#CBD2D9]">/</span>
    <span class="font-semibold text-unicaes-ink">{{ $faculty->name }}</span>
</nav>

@php($initials = collect(explode(' ', str_ireplace(['Facultad de ', 'Facultad '], '', $faculty->name)))
    ->reject(fn ($w) => in_array(mb_strtolower($w), ['de', 'del', 'la', 'las', 'los', 'y', 'e', 'en']))
    ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode(''))

<div class="mb-9">
    <div class="flex items-center gap-3">
        <span class="flex size-11 shrink-0 items-center justify-center rounded-[10px] bg-unicaes-maroon/10 text-xs font-extrabold tracking-wide text-unicaes-maroon">
            {{ $initials }}
        </span>
        <h1 class="text-xl font-extrabold tracking-tight sm:text-[22px]">{{ $faculty->name }}</h1>
    </div>
    <p class="mt-1.5 text-[13px] text-unicaes-slate sm:ml-14">
        {{ $careers->count() }} {{ Str::plural('carrera', $careers->count()) }}@if($faculty->description) · {{ $faculty->description }}@endif
    </p>
</div>

@if($careers->isEmpty())
    <div class="card p-10 text-center text-sm text-unicaes-slate">
        Esta facultad aún no tiene carreras registradas.
    </div>
@else
    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($careers as $career)
            <li class="stagger-item" style="--i: {{ min($loop->index, 5) }}">
                <a href="{{ route('catalog.career', [$faculty, $career]) }}"
                   class="card card-interactive group flex h-full flex-col px-5 py-[18px]">
                    <div class="mb-3 flex items-start justify-between gap-2.5">
                        <h2 class="text-sm font-semibold leading-snug group-hover:text-unicaes-green-dark">{{ $career->name }}</h2>
                        <svg class="mt-0.5 size-3.5 shrink-0 text-unicaes-line transition-colors group-hover:text-unicaes-slate" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                        </svg>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        @if($career->plan_year)
                            <span class="rounded-full border border-unicaes-line bg-unicaes-mist px-2.5 py-0.5 text-[11px] text-unicaes-slate">Plan {{ $career->plan_year }}</span>
                        @endif
                        <span class="rounded-full border border-unicaes-line bg-unicaes-mist px-2.5 py-0.5 text-[11px] text-unicaes-slate">
                            {{ $career->subjects_count }} {{ Str::plural('materia', $career->subjects_count) }}
                        </span>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
@endif
@endsection
