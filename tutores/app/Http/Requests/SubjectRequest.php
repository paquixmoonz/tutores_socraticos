<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:20'],
            'subject_category_id' => ['required', 'exists:subject_categories,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $teacherId = $this->input('teacher_id');
                if ($teacherId && ! User::find($teacherId)?->hasAnyRole('docente', 'admin')) {
                    $validator->errors()->add('teacher_id', 'El usuario seleccionado no tiene rol de docente.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'code' => 'código',
            'subject_category_id' => 'categoría',
            'teacher_id' => 'docente encargado',
            'description' => 'descripción',
        ];
    }
}
