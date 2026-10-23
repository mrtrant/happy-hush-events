<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventTypeController extends Controller
{
    public function index()
    {
        $eventTypes = EventType::latest()->paginate(10);
        return view('admin.event-types.index', compact('eventTypes'));
    }

    public function create()
    {
        return view('admin.event-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name',
            'description' => 'nullable|string',
        ]);

        $validated['uuid'] = (string) Str::uuid();
        $validated['slug'] = Str::slug($request->name);
        $validated['is_active'] = $request->has('is_active');

        EventType::create($validated);

        return redirect()->route('admin.event-types.index')
            ->with('success', 'Event Type created successfully!');
    }

    public function edit(EventType $eventType)
    {
        return view('admin.event-types.edit', compact('eventType'));
    }

    public function update(Request $request, EventType $eventType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name,' . $eventType->id,
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['is_active'] = $request->has('is_active');

        $eventType->update($validated);

        return redirect()->route('admin.event-types.index')
            ->with('success', 'Event Type updated successfully!');
    }

    public function destroy(EventType $eventType)
    {
        $eventType->delete();
        return redirect()->route('admin.event-types.index')
            ->with('success', 'Event Type deleted successfully!');
    }
}
