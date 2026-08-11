@extends('layouts.app')

@section('title', 'Facultades')

@section('content')
@include('admin._nav')

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold tracking-tight">Facultades</h1>
    <a href="{{ route('admin.facultades.create') }}" class="btn-primary">Nueva facultad</a>
</div>

<div class="card overflow-hidden">
    @if($faculties->isEmpty())
        <p class="p-10 text-center text-sm text-unicaes-slate">Aún no hay facultades. Crea la primera.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] divide-y divide-unicaes-line">
                <thead class="bg-unicaes-mist/60">
                    <tr>
                        <th class="table-th">Nombre</th>
                        <th class="table-th">Carreras</th>
                        <th class="table-th">Estado</th>
                        <th class="table-th"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicaes-line">
                    @foreach($faculties as $faculty)
                        <tr>
                            <td class="table-td font-medium">{{ $faculty->name }}</td>
                            <td class="table-td text-unicaes-slate">{{ $faculty->careers_count }}</td>
                            <td class="table-td">
                                @if($faculty->is_active)
                                    <span class="rounded-full bg-unicaes-green/15 px-2.5 py-0.5 text-xs font-semibold text-unicaes-green-dark">Activa</span>
                                @else
                                    <span class="rounded-full bg-unicaes-mist px-2.5 py-0.5 text-xs font-semibold text-unicaes-slate">Inactiva</span>
                                @endif
                            </td>
                            <td class="table-td">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.facultades.edit', $faculty) }}" class="text-sm font-medium text-unicaes-blue hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('admin.facultades.destroy', $faculty) }}"
                                          onsubmit="return confirm('¿Eliminar la facultad {{ $faculty->name }}?')">
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
        <div class="border-t border-unicaes-line px-4 py-3">{{ $faculties->links() }}</div>
    @endif
</div>
@endsection
