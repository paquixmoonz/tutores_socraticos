<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('subject_categories')->ignore($this->route('category')),
            ],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return ['color.regex' => 'El color debe ser un valor hexadecimal, por ejemplo #3AAA35.'];
    }

    public function attributes(): array
    {
        return ['name' => 'nombre', 'color' => 'color', 'description' => 'descripción'];
    }
}
