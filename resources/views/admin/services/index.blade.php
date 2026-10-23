@extends('layouts.admin')

@section('title', 'Services Management')
@section('page-title', 'Services')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Manage event services, pricing and availability.</p>
        </div>
        <a href="{{ route('admin.services.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
            Add Service
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm">
        {{ session('success') }}
    </div>
    @endif

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

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.services.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-5">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search services</label>
                    <input type="text" id="search" name="search"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           value="{{ request('search') }}"
                           placeholder="Name, code or description">
                </div>

                <!-- Category Filter -->
                <div class="md:col-span-3">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" id="category_id" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-900 shadow-sm transition-colors">Filter</button>
                    <a href="{{ route('admin.services.index') }}" class="px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 shadow-sm text-center transition-colors">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Service</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Price (KSh)</th>
                        <th class="px-6 py-3">Pricing Type</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse($services as $service)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($service->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($service->image) }}"
                                     alt="{{ $service->name }}"
                                     class="w-12 h-12 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                                @else
                                <div class="w-12 h-12 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 text-xs flex-shrink-0">
                                    No img
                                </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $service->name }}</div>
                                    @if($service->duration_minutes)
                                    <div class="text-xs text-gray-500">{{ $service->duration_minutes }} minutes</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $service->service_code }}</td>
                        <td class="px-6 py-4">{{ $service->category_name ?? 'Uncategorised' }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ number_format((float) $service->base_price, 2) }}</td>
                        <td class="px-6 py-4">{{ $service->pricing_type ?? 'Fixed' }}</td>
                        <td class="px-6 py-4">
                            @if($service->is_active)
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-3 justify-end">
                                <a href="{{ route('admin.services.edit', $service->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.services.toggle-status', $service->id) }}" onsubmit="return confirm('Change this service status?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-600 hover:text-gray-900 font-medium text-xs">
                                        {{ $service->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No services found. Add a service or change your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $services->links() }}
        </div>
    </div>
</div>
@endsection
