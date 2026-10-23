@extends('layouts.admin')

@section('title', 'Add Event Type')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-gray-800 mb-1">Add New Event Type</h2>
            <p class="text-sm text-gray-500 mb-0">Create a new category for event bookings.</p>
        </div>
        <a href="{{ route('admin.event-types.index') }}" class="btn btn-outline-secondary text-sm font-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card bg-white border border-gray-200 shadow-sm rounded-lg p-6 max-w-2xl">
        <form action="{{ route('admin.event-types.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Event Type Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="form-control w-full border-gray-300 rounded-lg shadow-sm @error('name') is-invalid @enderror"
                    placeholder="e.g., Wedding, Corporate Gala">
                @error('name')
                    <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                <textarea name="description" id="description" rows="4"
                    class="form-control w-full border-gray-300 rounded-lg shadow-sm @error('description') is-invalid @enderror"
                    placeholder="Brief details about this event type...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4">
                <label for="is_active" class="ms-2 block text-sm text-gray-900 font-medium">Active Status</label>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.event-types.index') }}" class="btn btn-light px-4 py-2 border rounded-lg text-sm font-semibold">Cancel</a>
                <button type="submit" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm">Save Event Type</button>
            </div>
        </form>
    </div>
</div>
@endsection
