@extends('layouts.admin')

@section('title', 'Testimonials Management')
@section('page-title', 'Testimonials Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Testimonials Management</h2>
        <a href="{{ route('admin.testimonials.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 text-sm font-medium">+ Add Testimonial</a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white p-4 shadow-sm sm:rounded-lg mb-6 border border-gray-200">
        <form method="GET" action="{{ route('admin.testimonials.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client name or message..."
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
            </div>
            <div>
                <select name="approval" class="w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
                    <option value="">All Approvals</option>
                    <option value="1" {{ request('approval') === '1' ? 'selected' : '' }}>Approved</option>
                    <option value="0" {{ request('approval') === '0' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700 w-full">Filter</button>
                <a href="{{ route('admin.testimonials.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200 text-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client / Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approval</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($testimonials as $t)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                @if($t->image_path)
                                    <img src="{{ asset('storage/' . $t->image_path) }}" class="h-10 w-10 rounded-full object-cover">
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $t->name }}</div>
                                    <div class="text-xs text-yellow-500 font-bold">★ {{ $t->rating }}/5</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-md">
                            <p class="line-clamp-2">"{{ $t->message }}"</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $t->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $t->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <form action="{{ route('admin.testimonials.approval', $t->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-blue-600 hover:underline">Toggle Approval</button>
                            </form>
                            <a href="{{ route('admin.testimonials.edit', $t->id) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.testimonials.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete testimonial?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No testimonials found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $testimonials->links() }}
    </div>
</div>
@endsection
