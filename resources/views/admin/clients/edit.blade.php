@extends('layouts.admin')

@section('title', 'Edit Client')
@section('page-title', 'Edit Client')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">{{ $client->client_number }}</p>
        </div>
        <a href="{{ route('admin.clients.show', $client->id) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
            Cancel
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm shadow-sm">
        <div class="font-semibold mb-1">Please correct the following:</div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <form method="POST" action="{{ route('admin.clients.update', $client->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Personal Information Section -->
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" id="first_name" name="first_name" required
                               value="{{ old('first_name', $client->first_name) }}"
                               class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name"
                               value="{{ old('middle_name', $client->middle_name) }}"
                               class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" required
                               value="{{ old('last_name', $client->last_name) }}"
                               class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required
                               value="{{ old('email', $client->email) }}"
                               class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" id="phone" name="phone"
                               value="{{ old('phone', $client->phone) }}"
                               class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Client Information Section -->
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Client Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="client_type" class="block text-sm font-medium text-gray-700 mb-1">Client Type <span class="text-red-500">*</span></label>
                        <select id="client_type" name="client_type" required
                                class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($clientTypes as $type)
                            <option value="{{ $type }}" @selected(old('client_type', $client->client_type) === $type)>
                                {{ $type }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="preferred_contact_method" class="block text-sm font-medium text-gray-700 mb-1">Preferred Contact Method</label>
                        <select id="preferred_contact_method" name="preferred_contact_method"
                                class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select method</option>
                            @foreach($contactMethods as $method)
                            <option value="{{ $method }}" @selected(old('preferred_contact_method', $client->preferred_contact_method) === $method)>
                                {{ $method }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-1">Organization Name</label>
                        <input type="text" id="organization_name" name="organization_name"
                               value="{{ old('organization_name', $client->organization_name) }}"
                               class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="kra_pin" class="block text-sm font-medium text-gray-700 mb-1">KRA PIN</label>
                        <input type="text" id="kra_pin" name="kra_pin"
                               value="{{ old('kra_pin', $client->kra_pin) }}"
                               class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" id="website" name="website" placeholder="https://example.com"
                               value="{{ old('website', $client->website) }}"
                               class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="5"
                                  class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $client->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.clients.show', $client->id) }}"
                   class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 shadow-sm transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
