<?php

namespace App\Http\Requests;

use App\Models\Cycle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubjectAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'career_id' => ['required', 'exists:careers,id'],
            'cycle_id' => ['required', 'exists:cycles,id'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $cycle = Cycle::find($this->input('cycle_id'));
                if ($cycle && (int) $cycle->career_id !== (int) $this->input('career_id')) {
                    $validator->errors()->add('cycle_id', 'El ciclo no pertenece a la carrera seleccionada.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return ['career_id' => 'carrera', 'cycle_id' => 'ciclo'];
    }
}
