<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $config = config('tutores.uploads.agent');

        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'file' => [
                'required',
                File::types($config['extensions'])->max($config['max_kb']),
            ],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nombre', 'description' => 'descripción', 'file' => 'archivo'];
    }
}
