<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ServiceManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceManagementService $serviceManagement
    ) {}

    public function index(Request $request): View
    {
        $query = DB::table('services as s')
            ->leftJoin('service_categories as c', 'c.id', '=', 's.category_id')
            ->select('s.*', 'c.name as category_name')
            ->whereNull('s.deleted_at');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($builder) use ($search): void {
                $builder->where('s.name', 'like', "%{$search}%")
                    ->orWhere('s.service_code', 'like', "%{$search}%")
                    ->orWhere('s.description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('s.category_id', $request->integer('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('s.is_active', $request->string('status')->toString() === 'active' ? 1 : 0);
        }

        $services = $query
            ->orderByDesc('s.created_at')
            ->paginate(15)
            ->withQueryString();

        $categories = $this->fetchCategories();

        return view('admin.services.index', compact('services', 'categories'));
    }

    public function create(): View
    {
        return view('admin.services.create', [
            'service'    => null,
            'categories' => $this->fetchCategories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateService($request);
        $image = $request->file('image')?->store('services', 'public');

        try {
            $this->serviceManagement->create($validated, $image);
        } catch (Throwable $exception) {
            if ($image) {
                Storage::disk('public')->delete($image);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(int $service): View
    {
        $record = DB::table('services')
            ->where('id', $service)
            ->whereNull('deleted_at')
            ->first();

        abort_if(! $record, 404);

        return view('admin.services.edit', [
            'service'    => $record,
            'categories' => $this->fetchCategories(),
        ]);
    }

    public function update(Request $request, int $service): RedirectResponse
    {
        $record = DB::table('services')
            ->where('id', $service)
            ->whereNull('deleted_at')
            ->first();

        abort_if(! $record, 404);

        $validated = $this->validateService($request, $service);
        $newImage = $request->file('image')?->store('services', 'public');

        try {
            $this->serviceManagement->update(
                $service,
                $validated,
                $newImage
            );
        } catch (Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        if ($newImage && $record->image) {
            Storage::disk('public')->delete($record->image);
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function toggleStatus(int $service): RedirectResponse
    {
        $this->serviceManagement->toggleStatus($service);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service status updated.');
    }

    private function validateService(Request $request, ?int $serviceId = null): array
    {
        return $request->validate([
            'category_id' => [
                'required',
                'integer',
                'exists:service_categories,id',
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'pricing_type' => [
                'required',
                Rule::in(['Fixed', 'Per Person', 'Per Hour', 'Custom']),
            ],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function fetchCategories(): Collection
    {
        return DB::table('service_categories')
            ->when(
                Schema::hasColumn('service_categories', 'deleted_at'),
                fn ($q) => $q->whereNull('deleted_at')
            )
            ->orderBy('name')
            ->get();
    }
}
