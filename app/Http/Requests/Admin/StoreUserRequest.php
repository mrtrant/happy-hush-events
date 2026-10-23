<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:roles,name',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'distinct',
                Rule::exists('permissions', 'id'),
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
