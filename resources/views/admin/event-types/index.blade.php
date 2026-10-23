@extends('layouts.admin')

@section('title', 'Manage Event Types')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-gray-800 mb-1">Event Types</h2>
            <p class="text-sm text-gray-500 mb-0">Manage event categories available for client bookings.</p>
        </div>
        <a href="{{ route('admin.event-types.create') }}" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Event Type
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm" role="alert">
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="card bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">Slug</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($eventTypes as $eventType)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 font-medium text-gray-900">{{ $eventType->name }}</td>
                            <td class="py-4 px-6 text-gray-500 font-mono text-xs">{{ $eventType->slug }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $eventType->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $eventType->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-end space-x-2">
                                <a href="{{ route('admin.event-types.edit', $eventType) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                <form action="{{ route('admin.event-types.destroy', $eventType) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this event type?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">
                                No event types found. Click "Add Event Type" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($eventTypes->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $eventTypes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
