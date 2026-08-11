@extends('layouts.app')

@section('title', $career->exists ? 'Editar carrera' : 'Nueva carrera')

@section('content')
@include('admin._nav')

<div class="mx-auto max-w-xl">
    <h1 class="mb-6 text-2xl font-bold tracking-tight">
        {{ $career->exists ? 'Editar carrera' : 'Nueva carrera' }}
    </h1>

    <form method="POST"
          action="{{ $career->exists ? route('admin.carreras.update', $career) : route('admin.carreras.store') }}"
          class="card space-y-5 p-6 sm:p-8">
        @csrf
        @if($career->exists) @method('PUT') @endif

        <div>
            <label for="faculty_id" class="field-label">Facultad</label>
            <select id="faculty_id" name="faculty_id" required class="field-input">
                <option value="" disabled @selected(!old('faculty_id', $career->faculty_id))>Selecciona una facultad…</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" @selected((int) old('faculty_id', $career->faculty_id) === $faculty->id)>
                        {{ $faculty->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="name" class="field-label">Nombre de la carrera</label>
            <input id="name" name="name" type="text" required maxlength="150"
                   value="{{ old('name', $career->name) }}" class="field-input">
        </div>

        <div>
            <label for="plan_year" class="field-label">Año de plan <span class="font-normal text-unicaes-slate">(opcional, ej. 2021-2025)</span></label>
            <input id="plan_year" name="plan_year" type="text" maxlength="20"
                   value="{{ old('plan_year', $career->plan_year) }}" class="field-input">
        </div>

        @if($career->exists)
            <label class="flex items-center gap-2.5 text-sm font-medium">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $career->is_active))
                       class="size-4 rounded border-unicaes-line text-unicaes-green focus:ring-unicaes-green/30">
                Carrera activa (visible en el catálogo)
            </label>
        @endif

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">{{ $career->exists ? 'Guardar cambios' : 'Crear carrera' }}</button>
            <a href="{{ route('admin.carreras.index') }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
