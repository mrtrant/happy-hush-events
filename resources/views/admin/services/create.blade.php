@extends('layouts.admin')

@section('title', 'Add Service')
@section('page-title', 'Add Service')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Create a new service offered by Happy Hush Events.</p>
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
        <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Service Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Service Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" maxlength="150" required
                           value="{{ old('name') }}"
                           placeholder="e.g. Premium DJ & Sound Package"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('category_id') border-red-500 @enderror">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                              placeholder="Provide details about what is included in this service..."
                              class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Base Price -->
                <div>
                    <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Base Price (KSh) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">KSh</span>
                        <input type="number" id="base_price" name="base_price" min="0" step="0.01" required
                               value="{{ old('base_price', '0.00') }}"
                               class="w-full rounded-lg border-gray-300 border pl-12 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('base_price') border-red-500 @enderror">
                    </div>
                    @error('base_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pricing Type -->
                <div>
                    <label for="pricing_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Pricing Type <span class="text-red-500">*</span>
                    </label>
                    <select id="pricing_type" name="pricing_type" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @foreach(['Fixed', 'Per Person', 'Per Hour', 'Custom'] as $type)
                        <option value="{{ $type }}" @selected(old('pricing_type', 'Fixed') === $type)>
                            {{ $type }}
                        </option>
                        @endforeach
                    </select>
                    @error('pricing_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration (minutes) -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                        Duration (Minutes)
                    </label>
                    <input type="number" id="duration_minutes" name="duration_minutes" min="1"
                           value="{{ old('duration_minutes') }}"
                           placeholder="e.g. 120"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('duration_minutes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="is_active" name="is_active" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="1" @selected(old('is_active', '1') == '1')>Active</option>
                        <option value="0" @selected(old('is_active') == '0')>Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Image -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        Service Image
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h20a4 4 0 004-4V20m-6-12l-6-6m0 0l-6 6m6-6v18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Upload an image</span>
                                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">JPG, PNG or WebP up to 4 MB</p>
                        </div>
                    </div>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-gray-200 flex items-center gap-3">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Save Service
                </button>
                <a href="{{ route('admin.services.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 shadow-sm transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
