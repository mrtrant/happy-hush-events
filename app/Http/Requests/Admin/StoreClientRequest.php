<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'client_type' => [
                'required',
                Rule::in([
                    'Individual',
                    'Company',
                    'Organization',
                    'School',
                    'Church',
                    'Government',
                ]),
            ],

            'organization_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'kra_pin' => [
                'nullable',
                'string',
                'max:30',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'preferred_contact_method' => [
                'required',
                Rule::in([
                    'Email',
                    'Phone',
                    'SMS',
                    'WhatsApp',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
