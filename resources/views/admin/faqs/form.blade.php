@extends('layouts.admin')

@section('title', isset($faq) ? 'Edit FAQ' : 'Add New FAQ')
@section('page-title', isset($faq) ? 'Edit FAQ' : 'Add New FAQ')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ isset($faq) ? 'Edit FAQ' : 'Create New FAQ' }}</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 p-4 rounded text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($faq) ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}"
              method="POST" class="space-y-6">
            @csrf
            @if(isset($faq))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Question *</label>
                <input type="text" name="question" value="{{ old('question', $faq->question ?? '') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Answer *</label>
                <textarea name="answer" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">{{ old('answer', $faq->answer ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', $faq->display_order ?? 0) }}" min="0"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active / Visible publicly</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.faqs.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">Cancel</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Save FAQ</button>
            </div>
        </form>
    </div>
</div>
@endsection
