@extends('layouts.app')

@section('title', 'Administración')

@section('content')
@include('admin._nav')

<div class="mb-6">
    <h1 class="text-xl font-extrabold tracking-tight sm:text-[22px]">Administración</h1>
    <p class="mt-1.5 text-[13px] text-unicaes-slate">Estado general del catálogo institucional.</p>
</div>

<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    @foreach([
        ['label' => 'Facultades', 'value' => $stats['faculties'], 'route' => route('admin.facultades.index')],
        ['label' => 'Carreras', 'value' => $stats['careers'], 'route' => route('admin.carreras.index')],
        ['label' => 'Materias', 'value' => $stats['subjects'], 'route' => route('admin.materias.index')],
        ['label' => 'Usuarios', 'value' => $stats['users'], 'route' => route('admin.users.index')],
    ] as $stat)
        <a href="{{ $stat['route'] }}"
           class="card-interactive stagger-item rounded-[14px] border border-unicaes-line bg-white px-[22px] py-5"
           style="--i: {{ $loop->index }}">
            <p data-ticker class="text-[28px] font-extrabold leading-none tracking-tight tabular-nums">{{ $stat['value'] }}</p>
            <p class="mt-1.5 text-xs text-unicaes-slate">{{ $stat['label'] }}</p>
        </a>
    @endforeach
</div>

<div class="mt-3 grid grid-cols-2 gap-3 lg:grid-cols-4">
    <div class="stagger-item rounded-[14px] border border-unicaes-line bg-white px-[22px] py-5" style="--i: 4">
        <p data-ticker class="text-[28px] font-extrabold leading-none tracking-tight tabular-nums text-unicaes-green">{{ $stats['agents'] }}</p>
        <p class="mt-1.5 text-xs text-unicaes-slate">Agentes activos</p>
    </div>
    <div class="stagger-item rounded-[14px] border border-unicaes-line bg-white px-[22px] py-5" style="--i: 5">
        <p data-ticker class="text-[28px] font-extrabold leading-none tracking-tight tabular-nums text-unicaes-blue">{{ $stats['plans'] }}</p>
        <p class="mt-1.5 text-xs text-unicaes-slate">Planes publicados</p>
    </div>
    <div class="stagger-item rounded-[14px] border border-unicaes-line bg-white px-[22px] py-5" style="--i: 6">
        <p data-ticker class="text-[28px] font-extrabold leading-none tracking-tight tabular-nums text-unicaes-gold">{{ $stats['downloads'] }}</p>
        <p class="mt-1.5 text-xs text-unicaes-slate">Descargas totales</p>
    </div>
    @if($stats['subjects_without_agent'] > 0)
        <div class="stagger-item rounded-[14px] border border-amber-200 bg-amber-50 px-[22px] py-5" style="--i: 7">
            <p data-ticker class="text-[28px] font-extrabold leading-none tracking-tight tabular-nums text-unicaes-ink">{{ $stats['subjects_without_agent'] }}</p>
            <p class="mt-1.5 text-xs font-semibold text-amber-700">Materias sin agente</p>
        </div>
    @else
        <div class="stagger-item rounded-[14px] border border-unicaes-line bg-white px-[22px] py-5" style="--i: 7">
            <p class="text-[28px] font-extrabold leading-none tracking-tight tabular-nums">0</p>
            <p class="mt-1.5 text-xs text-unicaes-slate">Materias sin agente</p>
        </div>
    @endif
</div>
@endsection
