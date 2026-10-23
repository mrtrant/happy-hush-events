@extends('layouts.admin')

@section('title', 'Add Package')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Add New Package</h2>
            <p class="text-muted small mb-0">Create a pricing structure and package option for event types.</p>
        </div>
        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Packages
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4 max-w-2xl bg-white" style="max-width: 800px;">
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="event_type_id" class="form-label fw-semibold">Related Event Type (Optional)</label>
                <select name="event_type_id" id="event_type_id"
                    class="form-select @error('event_type_id') is-invalid @enderror">
                    <option value="">-- General / All Event Types --</option>
                    @foreach($eventTypes as $eventType)
                        <option value="{{ $eventType->id }}" {{ old('event_type_id') == $eventType->id ? 'selected' : '' }}>
                            {{ $eventType->name }}
                        </option>
                    @endforeach
                </select>
                @error('event_type_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Package Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="e.g., Gold Wedding Package">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label fw-semibold">Base Price (KES)</label>
                <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" required
                    class="form-control @error('price') is-invalid @enderror"
                    placeholder="e.g., 150000">
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description / Inclusions</label>
                <textarea name="description" id="description" rows="4"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="What's included in this package...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="form-check-input">
                <label for="is_active" class="form-check-label fw-semibold">Active on Booking Forms</label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.packages.index') }}" class="btn btn-light border px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Save Package</button>
            </div>
        </form>
    </div>
</div>
@endsection
