<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Un admin no puede cambiarse el rol a sí mismo: evita quedarse sin admins.
        return $this->user()->isAdmin()
            && $this->user()->isNot($this->route('user'));
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(['estudiante', 'docente', 'admin'])],
        ];
    }

    public function attributes(): array
    {
        return ['role' => 'rol'];
    }
}
