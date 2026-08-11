@extends('layouts.app')

@section('title', 'Carreras')

@section('content')
@include('admin._nav')

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold tracking-tight">Carreras</h1>
    <a href="{{ route('admin.carreras.create') }}" class="btn-primary">Nueva carrera</a>
</div>

<form method="GET" action="{{ route('admin.carreras.index') }}" class="mb-4 flex max-w-md gap-2">
    <label for="faculty" class="sr-only">Filtrar por facultad</label>
    <select id="faculty" name="faculty" class="field-input" onchange="this.form.submit()">
        <option value="">Todas las facultades</option>
        @foreach($faculties as $faculty)
            <option value="{{ $faculty->id }}" @selected($facultyId === $faculty->id)>{{ $faculty->name }}</option>
        @endforeach
    </select>
</form>

<div class="card overflow-hidden">
    @if($careers->isEmpty())
        <p class="p-10 text-center text-sm text-unicaes-slate">No hay carreras con ese filtro.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] divide-y divide-unicaes-line">
                <thead class="bg-unicaes-mist/60">
                    <tr>
                        <th class="table-th">Carrera</th>
                        <th class="table-th">Facultad</th>
                        <th class="table-th">Ciclos</th>
                        <th class="table-th">Materias</th>
                        <th class="table-th">Estado</th>
                        <th class="table-th"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicaes-line">
                    @foreach($careers as $career)
                        <tr>
                            <td class="table-td">
                                <a href="{{ route('admin.carreras.show', $career) }}" class="font-medium text-unicaes-blue hover:underline">{{ $career->name }}</a>
                                @if($career->plan_year)<span class="block text-xs text-unicaes-slate">Plan {{ $career->plan_year }}</span>@endif
                            </td>
                            <td class="table-td text-unicaes-slate">{{ $career->faculty->name }}</td>
                            <td class="table-td text-unicaes-slate">{{ $career->cycles_count }}</td>
                            <td class="table-td text-unicaes-slate">{{ $career->subjects_count }}</td>
                            <td class="table-td">
                                @if($career->is_active)
                                    <span class="rounded-full bg-unicaes-green/15 px-2.5 py-0.5 text-xs font-semibold text-unicaes-green-dark">Activa</span>
                                @else
                                    <span class="rounded-full bg-unicaes-mist px-2.5 py-0.5 text-xs font-semibold text-unicaes-slate">Inactiva</span>
                                @endif
                            </td>
                            <td class="table-td">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.carreras.edit', $career) }}" class="text-sm font-medium text-unicaes-blue hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('admin.carreras.destroy', $career) }}"
                                          onsubmit="return confirm('¿Eliminar la carrera {{ $career->name }}?')">
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
        <div class="border-t border-unicaes-line px-4 py-3">{{ $careers->links() }}</div>
    @endif
</div>
@endsection
