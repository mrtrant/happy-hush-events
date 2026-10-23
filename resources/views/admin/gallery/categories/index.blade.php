@extends('layouts.admin')

@section('title', 'Gallery Categories')
@section('page-title', 'Gallery Categories')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Gallery Categories</h2>
        <a href="{{ route('admin.gallery.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow hover:bg-gray-300 text-sm font-medium">← Back to Gallery</a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Create Category Form -->
        <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200 h-fit">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add Category</h3>
            <form action="{{ route('admin.gallery.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name *</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="display_order" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                    <label for="is_active" class="ml-2 text-sm text-gray-900">Active</label>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Save Category</button>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="md:col-span-2 bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order / Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Images Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($categories as $cat)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $cat->name }} (Order: {{ $cat->display_order }})</div>
                                <div class="text-xs text-gray-500">{{ $cat->description }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $cat->images_count }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $cat->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <form action="{{ route('admin.gallery.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete category?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
