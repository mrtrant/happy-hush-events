@extends('layouts.admin')

@section('title', isset($testimonial) ? 'Edit Testimonial' : 'Add New Testimonial')
@section('page-title', isset($testimonial) ? 'Edit Testimonial' : 'Add New Testimonial')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ isset($testimonial) ? 'Edit Testimonial' : 'Add New Testimonial' }}</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 p-4 rounded text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($testimonial) ? route('admin.testimonials.update', $testimonial->id) : route('admin.testimonials.store') }}"
              method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($testimonial))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Client Name *</label>
                <input type="text" name="name" value="{{ old('name', $testimonial->name ?? '') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Rating (1 to 5) *</label>
                <select name="rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Testimonial Message *</label>
                <textarea name="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">{{ old('message', $testimonial->message ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Client Image / Avatar</label>
                @if(isset($testimonial) && $testimonial->image_path)
                    <div class="my-2">
                        <img src="{{ asset('storage/' . $testimonial->image_path) }}" class="h-16 w-16 rounded-full object-cover border">
                    </div>
                @endif
                <input type="file" name="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_approved" id="is_approved" value="1" {{ old('is_approved', $testimonial->is_approved ?? false) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_approved" class="ml-2 block text-sm text-gray-900">Approve for public display</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.testimonials.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">Cancel</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Save Testimonial</button>
            </div>
        </form>
    </div>
</div>
@endsection
