@extends('layouts.admin')

@section('title', 'FAQs Management')
@section('page-title', 'FAQs Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Frequently Asked Questions</h2>
        <a href="{{ route('admin.faqs.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 text-sm font-medium">+ Add FAQ</a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 shadow-sm sm:rounded-lg mb-6 border border-gray-200">
        <form method="GET" action="{{ route('admin.faqs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions or answers..."
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm border p-2">
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
                <a href="{{ route('admin.faqs.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200 text-center">Reset</a>
            </div>
        </form>
    </div>

    <!-- FAQs Table -->
    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order / Question</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Answer Preview</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($faqs as $faq)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $faq->question }}</div>
                            <div class="text-xs text-gray-400">Display Order: {{ $faq->display_order }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-md">
                            <p class="line-clamp-2">{{ $faq->answer }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $faq->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <form action="{{ route('admin.faqs.status', $faq->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-blue-600 hover:underline">Toggle</button>
                            </form>
                            <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete FAQ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No FAQs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $faqs->links() }}
    </div>
</div>
@endsection
