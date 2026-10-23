<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $booking = $this->route('booking');

        if (!$booking instanceof Booking) {
            return false;
        }

        return auth()->check()
            && $booking->client()
                ->where('user_id', auth()->id())
                ->exists()
            && in_array(
                $booking->status,
                ['Draft', 'Pending Review'],
                true
            );
    }

    public function rules(): array
    {
        return [
            'event_name' => [
                'required',
                'string',
                'max:150',
            ],

            'event_type_id' => [
                'required',
                'integer',
                Rule::exists('event_types', 'id')
                    ->where('is_active', true),
            ],

            'venue_id' => [
                'nullable',
                'integer',
                Rule::exists('venues', 'id')
                    ->where('is_active', true),
            ],

            'package_id' => [
                'nullable',
                'integer',
                Rule::exists('packages', 'id')
                    ->where('is_active', true),
            ],

            'event_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after:start_time',
            ],

            'guest_count' => [
                'required',
                'integer',
                'min:1',
                'max:100000',
            ],

            'special_requirements' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'client_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'services' => [
                'nullable',
                'array',
            ],

            'services.*.service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')
                    ->where('is_active', true),
            ],

            'services.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],

            'services.*.notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
