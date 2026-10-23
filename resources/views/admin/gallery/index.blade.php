@extends('layouts.admin')

@section('title', 'Gallery Management')
@section('page-title', 'Gallery Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Gallery Management</h2>
        <div class="space-x-3">
            <a href="{{ route('admin.gallery.categories.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow hover:bg-gray-300 text-sm font-medium">Manage Categories</a>
            <a href="{{ route('admin.gallery.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 text-sm font-medium">+ Upload Image</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 shadow-sm sm:rounded-lg mb-6 border border-gray-200">
        <form method="GET" action="{{ route('admin.gallery.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or description..."
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
            </div>
            <div>
                <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700 w-full">Filter</button>
                <a href="{{ route('admin.gallery.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200 text-center">Reset</a>
            </div>
        </form>
    </div>

    <!-- Image Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($images as $img)
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="relative h-48 bg-gray-100">
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $img->title }}" class="w-full h-full object-cover">
                        <div class="absolute top-2 left-2 flex space-x-1">
                            @if($img->is_featured)
                                <span class="bg-yellow-500 text-white text-xs px-2 py-0.5 rounded font-semibold shadow">Featured</span>
                            @endif
                            <span class="{{ $img->is_active ? 'bg-green-500' : 'bg-red-500' }} text-white text-xs px-2 py-0.5 rounded font-semibold shadow">
                                {{ $img->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <span class="text-xs text-indigo-600 font-semibold uppercase tracking-wider">{{ $img->category_name ?? 'Uncategorized' }}</span>
                        <h3 class="text-md font-bold text-gray-900 mt-1 truncate">{{ $img->title }}</h3>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $img->description }}</p>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs">
                    <div class="flex space-x-2">
                        <form action="{{ route('admin.gallery.featured', $img->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-yellow-600 hover:underline font-medium">Feature</button>
                        </form>
                        <form action="{{ route('admin.gallery.status', $img->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-blue-600 hover:underline font-medium">Toggle</button>
                        </form>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.gallery.edit', $img->id) }}" class="text-indigo-600 hover:underline font-medium">Edit</a>
                        <form action="{{ route('admin.gallery.destroy', $img->id) }}" method="POST" onsubmit="return confirm('Delete this image?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline font-medium">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow p-12 text-center text-gray-500">
                No gallery images found.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $images->links() }}
    </div>
</div>
@endsection
