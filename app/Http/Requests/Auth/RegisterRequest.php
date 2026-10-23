<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
                Rule::unique('users', 'email'),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone'),
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:255',
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

            'website' => [
                'nullable',
                'url',
            ],

            'kra_pin' => [
                'nullable',
                'max:30',
            ],

        ];
    }
}
