@extends('layouts.app')

@section('title', $faculty->exists ? 'Editar facultad' : 'Nueva facultad')

@section('content')
@include('admin._nav')

<div class="mx-auto max-w-xl">
    <h1 class="mb-6 text-2xl font-bold tracking-tight">
        {{ $faculty->exists ? 'Editar facultad' : 'Nueva facultad' }}
    </h1>

    <form method="POST"
          action="{{ $faculty->exists ? route('admin.facultades.update', $faculty) : route('admin.facultades.store') }}"
          class="card space-y-5 p-6 sm:p-8">
        @csrf
        @if($faculty->exists) @method('PUT') @endif

        <div>
            <label for="name" class="field-label">Nombre</label>
            <input id="name" name="name" type="text" required maxlength="150"
                   value="{{ old('name', $faculty->name) }}" class="field-input">
        </div>

        <div>
            <label for="description" class="field-label">Descripción <span class="font-normal text-unicaes-slate">(opcional)</span></label>
            <textarea id="description" name="description" rows="3" maxlength="2000" class="field-input">{{ old('description', $faculty->description) }}</textarea>
        </div>

        @if($faculty->exists)
            <label class="flex items-center gap-2.5 text-sm font-medium">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faculty->is_active))
                       class="size-4 rounded border-unicaes-line text-unicaes-green focus:ring-unicaes-green/30">
                Facultad activa (visible en el catálogo)
            </label>
        @endif

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">{{ $faculty->exists ? 'Guardar cambios' : 'Crear facultad' }}</button>
            <a href="{{ route('admin.facultades.index') }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
