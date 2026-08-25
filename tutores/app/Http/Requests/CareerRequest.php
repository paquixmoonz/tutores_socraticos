<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'faculty_id' => ['required', 'exists:faculties,id'],
            'name' => [
                'required', 'string', 'max:150',
                // La misma carrera puede existir en varias facultades
                // (p. ej. "Licenciatura en Idiomas"), pero no dos veces en la misma.
                Rule::unique('careers')
                    ->where('faculty_id', $this->input('faculty_id'))
                    ->ignore($this->route('career')),
            ],
            'plan_year' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return ['name.unique' => 'Esa carrera ya existe en la facultad seleccionada.'];
    }

    public function attributes(): array
    {
        return ['faculty_id' => 'facultad', 'name' => 'nombre', 'plan_year' => 'año de plan'];
    }
}
