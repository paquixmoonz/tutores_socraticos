<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreStudyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('managePlans', $this->route('subject'));
    }

    public function rules(): array
    {
        $config = config('tutores.uploads.study_plan');

        return [
            'title' => ['required', 'string', 'max:150'],
            'file' => [
                'required',
                File::types($config['extensions'])->max($config['max_kb']),
            ],
        ];
    }

    public function attributes(): array
    {
        return ['title' => 'título', 'file' => 'archivo'];
    }
}
