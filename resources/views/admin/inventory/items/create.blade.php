<x-app-layout>
    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Add New Inventory Item</h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 text-red-700">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.inventory.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Inventory Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Inventory Code *</label>
                        <input type="text" name="inventory_code" value="{{ old('inventory_code') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category *</label>
                        <select name="category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Item Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Item Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">{{ old('description') }}</textarea>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Initial Quantity *</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="0" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>

                    <!-- Unit Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit Type</label>
                        <input type="text" name="unit_type" value="{{ old('unit_type', 'Piece') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>

                    <!-- Purchase Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>

                    <!-- Purchase Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Price</label>
                        <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>

                    <!-- Condition Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Condition Status *</label>
                        <select name="condition_status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                            @foreach(['New', 'Good', 'Needs Repair', 'Damaged', 'Retired'] as $status)
                                <option value="{{ $status }}" {{ old('condition_status', 'Good') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Storage Location</label>
                        <input type="text" name="location" value="{{ old('location') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    </div>

                    <!-- Image Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Item Image</label>
                        <input type="file" name="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <!-- Active Toggle -->
                    <div class="md:col-span-2 flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Item</label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.inventory.items.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">Cancel</a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
