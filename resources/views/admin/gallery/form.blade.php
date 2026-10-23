@extends('layouts.admin')

@section('title', isset($image) ? 'Edit Gallery Image' : 'Upload Gallery Image')
@section('page-title', isset($image) ? 'Edit Gallery Image' : 'Upload Gallery Image')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ isset($image) ? 'Edit Gallery Image' : 'Upload Gallery Image' }}</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 p-4 rounded text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($image) ? route('admin.gallery.update', $image->id) : route('admin.gallery.store') }}"
              method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($image))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Title *</label>
                <input type="text" name="title" value="{{ old('title', $image->title ?? '') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $image->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">{{ old('description', $image->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Image File {{ isset($image) ? '' : '*' }}</label>
                @if(isset($image) && $image->image_path)
                    <div class="my-2">
                        <img src="{{ asset('storage/' . $image->image_path) }}" class="h-24 w-24 object-cover rounded border">
                    </div>
                @endif
                <input type="file" name="image" {{ isset($image) ? '' : 'required' }} class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $image->is_featured ?? false) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_featured" class="ml-2 block text-sm text-gray-900">Feature on homepage/highlights</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $image->is_active ?? true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Active / Visible</label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.gallery.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">Cancel</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Save Image</button>
            </div>
        </form>
    </div>
</div>
@endsection
