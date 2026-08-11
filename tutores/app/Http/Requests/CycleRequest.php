<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'number' => [
                'required', 'integer', 'min:1', 'max:12',
                Rule::unique('cycles')->where('career_id', $this->route('career')->id),
            ],
            'name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return ['number.unique' => 'Ese ciclo ya existe en la carrera.'];
    }

    public function attributes(): array
    {
        return ['number' => 'número de ciclo', 'name' => 'nombre'];
    }
}
