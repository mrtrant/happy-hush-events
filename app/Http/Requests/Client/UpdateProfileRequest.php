<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore(auth()->id()),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
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
                'nullable',
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
                'max:5000',
            ],
        ];
    }
}
