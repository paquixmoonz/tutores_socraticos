@extends('layouts.app')

@section('title', $career->name)

@section('content')
@include('admin._nav')

<nav aria-label="Ruta de navegación" class="mb-6 text-sm text-unicaes-slate">
    <a href="{{ route('admin.carreras.index') }}" class="hover:text-unicaes-green-dark font-medium">Carreras</a>
    <span class="mx-1.5">/</span>
    <span class="font-medium text-unicaes-ink">{{ $career->name }}</span>
</nav>

<div class="mb-8 flex flex-wrap items-start justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ $career->name }}</h1>
        <p class="mt-1 text-sm text-unicaes-slate">{{ $career->faculty->name }}@if($career->plan_year) · Plan {{ $career->plan_year }}@endif</p>
    </div>
    <a href="{{ route('admin.carreras.edit', $career) }}" class="btn-secondary">Editar carrera</a>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        @forelse($career->cycles as $cycle)
            @php($subjects = $subjectsByCycle->get($cycle->id, collect()))
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-unicaes-line px-6 py-4">
                    <h2 class="font-semibold">{{ $cycle->label() }}</h2>
                    <form method="POST" action="{{ route('admin.cycles.destroy', $cycle) }}"
                          onsubmit="return confirm('¿Eliminar el {{ $cycle->label() }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Eliminar ciclo</button>
                    </form>
                </div>
                @if($subjects->isEmpty())
                    <p class="px-6 py-5 text-sm text-unicaes-slate">Sin materias asignadas a este ciclo.</p>
                @else
                    <ul class="divide-y divide-unicaes-line">
                        @foreach($subjects as $subject)
                            <li class="flex items-center justify-between gap-3 px-6 py-3.5">
                                <div>
                                    <a href="{{ route('admin.materias.show', $subject) }}" class="text-sm font-medium text-unicaes-blue hover:underline">{{ $subject->name }}</a>
                                    <span class="block text-xs text-unicaes-slate">{{ $subject->category?->name }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @empty
            <div class="card p-10 text-center text-sm text-unicaes-slate">
                Esta carrera aún no tiene ciclos. Agrega el primero con el formulario.
            </div>
        @endforelse
    </div>

    <div>
        <div class="card p-6">
            <h2 class="font-semibold">Agregar ciclo</h2>
            <form method="POST" action="{{ route('admin.cycles.store', $career) }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="number" class="field-label">Número de ciclo (1–12)</label>
                    <input id="number" name="number" type="number" min="1" max="12" required
                           value="{{ old('number', ($career->cycles->max('number') ?? 0) + 1) }}" class="field-input">
                </div>
                <div>
                    <label for="cycle-name" class="field-label">Nombre <span class="font-normal text-unicaes-slate">(opcional)</span></label>
                    <input id="cycle-name" name="name" type="text" maxlength="100" value="{{ old('name') }}"
                           placeholder="Ej. Ciclo I" class="field-input">
                </div>
                <button type="submit" class="btn-primary w-full">Agregar ciclo</button>
            </form>
        </div>
    </div>
</div>
@endsection
