@extends('layouts.app')

@section('title', $subject->exists ? 'Editar materia' : 'Nueva materia')

@section('content')
@include('admin._nav')

<div class="mx-auto max-w-xl">
    <h1 class="mb-2 text-2xl font-bold tracking-tight">
        {{ $subject->exists ? 'Editar materia' : 'Nueva materia' }}
    </h1>
    @unless($subject->exists)
        <p class="mb-6 text-sm text-unicaes-slate">
            Toda materia necesita una categoría y un docente encargado. Después de crearla podrás asignarla a carreras y subir su agente.
        </p>
    @endunless

    <form method="POST"
          action="{{ $subject->exists ? route('admin.materias.update', $subject) : route('admin.materias.store') }}"
          class="card space-y-5 p-6 sm:p-8">
        @csrf
        @if($subject->exists) @method('PUT') @endif

        <div>
            <label for="name" class="field-label">Nombre de la materia</label>
            <input id="name" name="name" type="text" required maxlength="150"
                   value="{{ old('name', $subject->name) }}" class="field-input">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="code" class="field-label">Código <span class="font-normal text-unicaes-slate">(opcional)</span></label>
                <input id="code" name="code" type="text" maxlength="20"
                       value="{{ old('code', $subject->code) }}" class="field-input">
            </div>
            <div>
                <label for="subject_category_id" class="field-label">Categoría</label>
                <select id="subject_category_id" name="subject_category_id" required class="field-input">
                    <option value="" disabled @selected(!old('subject_category_id', $subject->subject_category_id))>Selecciona…</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('subject_category_id', $subject->subject_category_id) === $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="teacher_id" class="field-label">Docente encargado del plan de grado</label>
            <select id="teacher_id" name="teacher_id" class="field-input">
                <option value="">Por asignar…</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected((int) old('teacher_id', $subject->teacher_id) === $teacher->id)>
                        {{ $teacher->name }} — {{ $teacher->email }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-unicaes-slate">Solo aparecen usuarios con rol docente. Asigna el rol desde la sección Usuarios.</p>
        </div>

        <div>
            <label for="description" class="field-label">Información de la materia</label>
            <textarea id="description" name="description" rows="4" maxlength="5000" class="field-input"
                      placeholder="Descripción que verá el estudiante…">{{ old('description', $subject->description) }}</textarea>
        </div>

        @if($subject->exists)
            <label class="flex items-center gap-2.5 text-sm font-medium">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $subject->is_active))
                       class="size-4 rounded border-unicaes-line text-unicaes-green focus:ring-unicaes-green/30">
                Materia activa (visible en el catálogo)
            </label>
        @endif

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">{{ $subject->exists ? 'Guardar cambios' : 'Crear materia' }}</button>
            <a href="{{ $subject->exists ? route('admin.materias.show', $subject) : route('admin.materias.index') }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
