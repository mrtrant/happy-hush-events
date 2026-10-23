<?php

declare(strict_types=1);

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ServiceManagementService
{
    public function create(array $data, ?string $image): int
    {
        return DB::transaction(function () use ($data, $image) {
            return DB::table('services')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'service_code' => $this->generateServiceCode(),
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'base_price' => $data['base_price'],
                'pricing_type' => $data['pricing_type'],
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'image' => $image,
                'is_active' => $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, 3);
    }

    public function update(int $id, array $data, ?string $newImage): void
    {
        DB::transaction(function () use ($id, $data, $newImage) {
            $service = DB::table('services')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (! $service) {
                throw ValidationException::withMessages([
                    'service' => 'The selected service no longer exists.',
                ]);
            }

            $changes = [
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'base_price' => $data['base_price'],
                'pricing_type' => $data['pricing_type'],
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'is_active' => $data['is_active'],
                'updated_at' => now(),
            ];

            if ($newImage !== null) {
                $changes['image'] = $newImage;
            }

            DB::table('services')->where('id', $id)->update($changes);
        }, 3);
    }

    public function toggleStatus(int $id): void
    {
        DB::transaction(function () use ($id) {
            $service = DB::table('services')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (! $service) {
                throw ValidationException::withMessages([
                    'service' => 'The selected service no longer exists.',
                ]);
            }

            DB::table('services')
                ->where('id', $id)
                ->update([
                    'is_active' => ! (bool) $service->is_active,
                    'updated_at' => now(),
                ]);
        }, 3);
    }

    private function generateServiceCode(): string
    {
        do {
            $code = 'HH-SVC-' . strtoupper(Str::random(8));
        } while (
            DB::table('services')->where('service_code', $code)->exists()
        );

        return $code;
    }
}
