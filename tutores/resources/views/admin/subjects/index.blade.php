@extends('layouts.app')

@section('title', 'Materias')

@section('content')
@include('admin._nav')

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold tracking-tight">Materias</h1>
    <a href="{{ route('admin.materias.create') }}" class="btn-primary">Nueva materia</a>
</div>

<form method="GET" action="{{ route('admin.materias.index') }}" class="mb-4 flex flex-wrap gap-2">
    <label for="q" class="sr-only">Buscar materia</label>
    <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Buscar por nombre o código…"
           class="field-input max-w-xs">
    <label for="categoria" class="sr-only">Filtrar por categoría</label>
    <select id="categoria" name="categoria" class="field-input max-w-56" onchange="this.form.submit()">
        <option value="">Todas las categorías</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-secondary">Filtrar</button>
</form>

<div class="card overflow-hidden">
    @if($subjects->isEmpty())
        <p class="p-10 text-center text-sm text-unicaes-slate">No hay materias con ese criterio.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] divide-y divide-unicaes-line">
                <thead class="bg-unicaes-mist/60">
                    <tr>
                        <th class="table-th">Materia</th>
                        <th class="table-th">Categoría</th>
                        <th class="table-th">Docente</th>
                        <th class="table-th">Agente</th>
                        <th class="table-th">Plan</th>
                        <th class="table-th"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicaes-line">
                    @foreach($subjects as $subject)
                        <tr>
                            <td class="table-td">
                                <a href="{{ route('admin.materias.show', $subject) }}" class="font-medium text-unicaes-blue hover:underline">{{ $subject->name }}</a>
                                @if($subject->code)<span class="block text-xs text-unicaes-slate">{{ $subject->code }}</span>@endif
                            </td>
                            <td class="table-td">
                                @if($subject->category)
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: {{ $subject->category->color ?? '#52606D' }}1a; color: {{ $subject->category->color ?? '#52606D' }}">
                                        {{ $subject->category->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="table-td text-unicaes-slate">{{ $subject->teacher?->name ?? '—' }}</td>
                            <td class="table-td">
                                <span class="text-sm font-medium {{ $subject->activeAgent ? 'text-unicaes-green-dark' : 'text-amber-600' }}">{{ $subject->activeAgent ? 'Sí' : 'No' }}</span>
                            </td>
                            <td class="table-td">
                                <span class="text-sm font-medium {{ $subject->activeStudyPlan ? 'text-unicaes-green-dark' : 'text-amber-600' }}">{{ $subject->activeStudyPlan ? 'Sí' : 'No' }}</span>
                            </td>
                            <td class="table-td">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.materias.edit', $subject) }}" class="text-sm font-medium text-unicaes-blue hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('admin.materias.destroy', $subject) }}"
                                          onsubmit="return confirm('¿Eliminar {{ $subject->name }}? Se borrarán también sus agentes y planes.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-unicaes-line px-4 py-3">{{ $subjects->links() }}</div>
    @endif
</div>
@endsection
