@extends('layouts.app')

@section('title', $category->exists ? 'Editar categoría' : 'Nueva categoría')

@section('content')
@include('admin._nav')

<div class="mx-auto max-w-xl">
    <h1 class="mb-6 text-2xl font-bold tracking-tight">
        {{ $category->exists ? 'Editar categoría' : 'Nueva categoría' }}
    </h1>

    <form method="POST"
          action="{{ $category->exists ? route('admin.categorias.update', $category) : route('admin.categorias.store') }}"
          class="card space-y-5 p-6 sm:p-8">
        @csrf
        @if($category->exists) @method('PUT') @endif

        <div>
            <label for="name" class="field-label">Nombre</label>
            <input id="name" name="name" type="text" required maxlength="100"
                   value="{{ old('name', $category->name) }}" placeholder="Ej. Numérica, Redacción…" class="field-input">
        </div>

        <div>
            <label for="color" class="field-label">Color para las etiquetas</label>
            <div class="flex items-center gap-3">
                <input id="color" name="color" type="color"
                       value="{{ old('color', $category->color ?? '#3AAA35') }}"
                       class="h-10 w-14 cursor-pointer rounded-lg border border-unicaes-line bg-white p-1">
                <span class="text-xs text-unicaes-slate">Se usa en las tarjetas del catálogo.</span>
            </div>
        </div>

        <div>
            <label for="description" class="field-label">Descripción <span class="font-normal text-unicaes-slate">(opcional)</span></label>
            <textarea id="description" name="description" rows="3" maxlength="2000" class="field-input">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">{{ $category->exists ? 'Guardar cambios' : 'Crear categoría' }}</button>
            <a href="{{ route('admin.categorias.index') }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
