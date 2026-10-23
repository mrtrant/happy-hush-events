<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\ClientProfile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileService
{
    public function profile(int $userId): ClientProfile
    {
        return ClientProfile::query()
            ->with([
                'user',
                'contacts' => fn ($query) => $query
                    ->orderByDesc('is_primary')
                    ->orderBy('first_name'),

                'addresses' => fn ($query) => $query
                    ->orderByDesc('is_default')
                    ->orderBy('address_type'),
            ])
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function updateProfile(
        ClientProfile $profile,
        array $data
    ): void {
        DB::transaction(function () use ($profile, $data): void {
            $profile->user->update([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            $profile->update([
                'client_type' => $data['client_type'],
                'organization_name' => $data['organization_name'] ?? null,
                'kra_pin' => $data['kra_pin'] ?? null,
                'website' => $data['website'] ?? null,
                'preferred_contact_method' =>
                    $data['preferred_contact_method'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function createContact(
        ClientProfile $profile,
        array $data
    ): ClientContact {
        return DB::transaction(
            function () use ($profile, $data): ClientContact {
                $isPrimary = (bool) ($data['is_primary'] ?? false);

                if ($isPrimary) {
                    $profile->contacts()->update([
                        'is_primary' => false,
                    ]);
                }

                return $profile->contacts()->create([
                    'uuid' => (string) Str::uuid(),
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'] ?? null,
                    'job_title' => $data['job_title'] ?? null,
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'alternative_phone' =>
                        $data['alternative_phone'] ?? null,
                    'is_primary' => $isPrimary,
                    'receives_notifications' =>
                        (bool) ($data['receives_notifications'] ?? true),
                ]);
            }
        );
    }

    public function updateContact(
        ClientProfile $profile,
        int $contactId,
        array $data
    ): ClientContact {
        return DB::transaction(
            function () use (
                $profile,
                $contactId,
                $data
            ): ClientContact {
                $contact = $profile->contacts()
                    ->whereKey($contactId)
                    ->first();

                if (!$contact) {
                    throw (new ModelNotFoundException())
                        ->setModel(
                            ClientContact::class,
                            [$contactId]
                        );
                }

                $isPrimary = (bool) ($data['is_primary'] ?? false);

                if ($isPrimary) {
                    $profile->contacts()
                        ->whereKey('!=', $contact->id)
                        ->update([
                            'is_primary' => false,
                        ]);
                }

                $contact->update([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'] ?? null,
                    'job_title' => $data['job_title'] ?? null,
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'alternative_phone' =>
                        $data['alternative_phone'] ?? null,
                    'is_primary' => $isPrimary,
                    'receives_notifications' =>
                        (bool) ($data['receives_notifications'] ?? true),
                ]);

                return $contact->refresh();
            }
        );
    }

    public function deleteContact(
        ClientProfile $profile,
        int $contactId
    ): void {
        $contact = $profile->contacts()
            ->whereKey($contactId)
            ->firstOrFail();

        $contact->delete();
    }

    public function createAddress(
        ClientProfile $profile,
        array $data
    ): ClientAddress {
        return DB::transaction(
            function () use ($profile, $data): ClientAddress {
                $isDefault = (bool) ($data['is_default'] ?? false);

                if ($isDefault) {
                    $profile->addresses()->update([
                        'is_default' => false,
                    ]);
                }

                return $profile->addresses()->create([
                    'uuid' => (string) Str::uuid(),
                    'address_type' => $data['address_type'],
                    'address_line_1' => $data['address_line_1'],
                    'address_line_2' => $data['address_line_2'] ?? null,
                    'city' => $data['city'] ?? null,
                    'county' => $data['county'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'country' => $data['country'] ?? 'Kenya',
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'is_default' => $isDefault,
                ]);
            }
        );
    }

    public function updateAddress(
        ClientProfile $profile,
        int $addressId,
        array $data
    ): ClientAddress {
        return DB::transaction(
            function () use (
                $profile,
                $addressId,
                $data
            ): ClientAddress {
                $address = $profile->addresses()
                    ->whereKey($addressId)
                    ->first();

                if (!$address) {
                    throw (new ModelNotFoundException())
                        ->setModel(
                            ClientAddress::class,
                            [$addressId]
                        );
                }

                $isDefault = (bool) ($data['is_default'] ?? false);

                if ($isDefault) {
                    $profile->addresses()
                        ->whereKey('!=', $address->id)
                        ->update([
                            'is_default' => false,
                        ]);
                }

                $address->update([
                    'address_type' => $data['address_type'],
                    'address_line_1' => $data['address_line_1'],
                    'address_line_2' => $data['address_line_2'] ?? null,
                    'city' => $data['city'] ?? null,
                    'county' => $data['county'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'country' => $data['country'] ?? 'Kenya',
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'is_default' => $isDefault,
                ]);

                return $address->refresh();
            }
        );
    }

    public function deleteAddress(
        ClientProfile $profile,
        int $addressId
    ): void {
        $address = $profile->addresses()
            ->whereKey($addressId)
            ->firstOrFail();

        $address->delete();
    }
}
