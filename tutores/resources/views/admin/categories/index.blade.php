@extends('layouts.app')

@section('title', 'Categorías de materia')

@section('content')
@include('admin._nav')

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold tracking-tight">Categorías de materia</h1>
    <a href="{{ route('admin.categorias.create') }}" class="btn-primary">Nueva categoría</a>
</div>

<div class="card overflow-hidden">
    @if($categories->isEmpty())
        <p class="p-10 text-center text-sm text-unicaes-slate">Aún no hay categorías.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px] divide-y divide-unicaes-line">
                <thead class="bg-unicaes-mist/60">
                    <tr>
                        <th class="table-th">Categoría</th>
                        <th class="table-th">Materias</th>
                        <th class="table-th"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicaes-line">
                    @foreach($categories as $category)
                        <tr>
                            <td class="table-td">
                                <span class="inline-flex items-center gap-2 font-medium">
                                    <span class="size-3 rounded-full" style="background-color: {{ $category->color ?? '#52606D' }}"></span>
                                    {{ $category->name }}
                                </span>
                            </td>
                            <td class="table-td text-unicaes-slate">{{ $category->subjects_count }}</td>
                            <td class="table-td">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.categorias.edit', $category) }}" class="text-sm font-medium text-unicaes-blue hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('admin.categorias.destroy', $category) }}"
                                          onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?')">
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
        <div class="border-t border-unicaes-line px-4 py-3">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
