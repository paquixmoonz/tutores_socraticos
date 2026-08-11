<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => ['required', 'string', 'max:150'],
            'plan_year' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['faculty_id' => 'facultad', 'name' => 'nombre', 'plan_year' => 'año de plan'];
    }
}
