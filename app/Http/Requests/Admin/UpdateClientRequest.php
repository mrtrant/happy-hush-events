<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $clientId = $this->route('client');

        $userId = DB::table('client_profiles')
            ->where('id', $clientId)
            ->value('user_id');

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
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'nullable',
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
