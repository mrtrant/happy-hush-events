@extends('layouts.admin')

@section('title', 'Edit Service')
@section('page-title', 'Edit Service')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">
                Update <span class="font-medium text-gray-800">{{ $service->name }}</span>. The service code remains unchanged.
            </p>
        </div>
        <a href="{{ route('admin.services.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
            &larr; Back to Services
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm shadow-sm">
        <div class="font-semibold mb-1">Please fix the following errors:</div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <form method="POST"
              action="{{ route('admin.services.update', $service->id) }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Service Code (Readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Code</label>
                <input type="text"
                       class="w-full rounded-lg border-gray-200 bg-gray-50 border px-3 py-2 text-sm text-gray-500 font-mono cursor-not-allowed"
                       value="{{ $service->service_code }}" readonly>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Service Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Service Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" maxlength="150" required
                           value="{{ old('name', $service->name) }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $service->category_id) === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('description', $service->description) }}</textarea>
                </div>

                <!-- Base Price -->
                <div>
                    <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Base Price (KSh) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">KSh</span>
                        <input type="number" id="base_price" name="base_price" min="0" step="0.01" required
                               value="{{ old('base_price', $service->base_price) }}"
                               class="w-full rounded-lg border-gray-300 border pl-12 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <!-- Pricing Type -->
                <div>
                    <label for="pricing_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Pricing Type <span class="text-red-500">*</span>
                    </label>
                    <select id="pricing_type" name="pricing_type" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @foreach(['Fixed', 'Per Person', 'Per Hour', 'Custom'] as $type)
                        <option value="{{ $type }}"
                            @selected(old('pricing_type', $service->pricing_type ?? 'Fixed') === $type)>
                            {{ $type }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Duration (minutes) -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                        Duration (Minutes)
                    </label>
                    <input type="number" id="duration_minutes" name="duration_minutes" min="1"
                           value="{{ old('duration_minutes', $service->duration_minutes) }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="is_active" name="is_active" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="1" @selected((string) old('is_active', $service->is_active) === '1')>Active</option>
                        <option value="0" @selected((string) old('is_active', $service->is_active) === '0')>Inactive</option>
                    </select>
                </div>

                <!-- Service Image Upload & Preview -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        Replace Service Image
                    </label>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep the existing image.</p>

                    @if($service->image)
                    <div class="mt-3 flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 max-w-sm">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($service->image) }}"
                             alt="{{ $service->name }}"
                             class="w-16 h-16 object-cover rounded-md border border-gray-300">
                        <div class="text-xs text-gray-600">
                            <span class="font-medium text-gray-900 block">Current Image</span>
                            Will be kept unless a new file is uploaded.
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-gray-200 flex items-center gap-3">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Update Service
                </button>
                <a href="{{ route('admin.services.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 shadow-sm transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
