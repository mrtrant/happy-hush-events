<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('eventType')->latest()->paginate(10);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $eventTypes = EventType::where('is_active', true)->get();
        return view('admin.packages.create', compact('eventTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => 'nullable|exists:event_types,id',
            'name'          => 'required|string|max:255|unique:packages,name',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
        ]);

        $validated['uuid']         = (string) Str::uuid();
        $validated['package_code'] = 'PKG-' . strtoupper(Str::random(6));
        $validated['slug']         = Str::slug($request->name);
        $validated['is_active']    = $request->has('is_active');

        Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully!');
    }

    public function edit(Package $package)
    {
        $eventTypes = EventType::where('is_active', true)->get();
        return view('admin.packages.edit', compact('package', 'eventTypes'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'event_type_id' => 'nullable|exists:event_types,id',
            'name'          => 'required|string|max:255|unique:packages,name,' . $package->id,
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
        ]);

        $validated['slug']      = Str::slug($request->name);
        $validated['is_active'] = $request->has('is_active');

        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully!');
    }
}
