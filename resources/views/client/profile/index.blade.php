@extends('layouts.client')

@section('title', 'My Profile')

@section('content')

<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                My Profile
            </h2>
            <p class="text-muted mb-0">
                Manage your personal information, emergency/business contacts, and delivery addresses.
            </p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-triangle-exclamation fs-4 me-2"></i>
                <div>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as$error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    {{-- PERSONAL INFORMATION SECTION --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-user-pen me-2" style="color: var(--brand-orange);"></i> Personal & Account Information
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('client.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $profile->user->first_name) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $profile->user->middle_name) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $profile->user->last_name) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $profile->user->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile->user->phone) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Client Number</label>
                        <input type="text" class="form-control bg-light" value="{{ $profile->client_number }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Client Type</label>
                        <select name="client_type" class="form-select" required>
                            @foreach(['Individual', 'Company', 'Organization', 'School', 'Church', 'Government'] as $type)
                                <option value="{{ $type }}" @selected(old('client_type', $profile->client_type) ===$type)>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Preferred Contact Method</label>
                        <select name="preferred_contact_method" class="form-select">
                            <option value="">Select Method</option>
                            @foreach(['Email', 'Phone', 'SMS', 'WhatsApp'] as $method)
                                <option value="{{ $method }}" @selected(old('preferred_contact_method', $profile->preferred_contact_method) ===$method)>
                                    {{ $method }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Organization Name</label>
                        <input type="text" name="organization_name" class="form-control" value="{{ old('organization_name', $profile->organization_name) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">KRA PIN</label>
                        <input type="text" name="kra_pin" class="form-control" value="{{ old('kra_pin', $profile->kra_pin) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Website</label>
                        <input type="url" name="website" class="form-control" value="{{ old('website', $profile->website) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes / Special Requirements</label>
                        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $profile->notes) }}</textarea>
                    </div>
                </div>

                <div class="mt-4 pt-2 border-top text-end">
                    <button type="submit" class="btn btn-brand px-4 py-2 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Profile Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- CONTACTS SECTION --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-address-book me-2" style="color: var(--brand-orange);"></i> Additional Contacts
            </h5>
            <button type="button" class="btn btn-brand btn-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addContactModal">
                <i class="fa-solid fa-plus me-1"></i> Add Contact
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3">Job Title</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Phone</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end px-4" width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($profile->contacts as$contact)
                        <tr>
                            <td class="px-4 fw-semibold text-dark">
                                {{ $contact->first_name }} {{$contact->last_name }}
                            </td>
                            <td>{{ $contact->job_title ?: '—' }}</td>
                            <td>{{ $contact->email ?: '—' }}</td>
                            <td>{{ $contact->phone ?: '—' }}</td>
                            <td>
                                @if($contact->is_primary)
                                    <span class="badge bg-success px-2 py-1">Primary</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1">Contact</span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <form method="POST" action="{{ route('client.profile.contacts.destroy', $contact->id) }}" onsubmit="return confirm('Are you sure you want to remove this contact?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fa-solid fa-address-book fs-2 text-secondary opacity-50 mb-2"></i>
                                <p class="mb-0">No additional contacts have been added yet.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ADDRESSES SECTION --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-location-dot me-2" style="color: var(--brand-orange);"></i> Addresses
            </h5>
            <button type="button" class="btn btn-brand btn-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="fa-solid fa-plus me-1"></i> Add Address
            </button>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @forelse($profile->addresses as$address)
                    <div class="col-lg-6">
                        <div class="border rounded-3 p-4 h-100 bg-white shadow-sm position-relative">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">
                                        <i class="fa-solid fa-map-pin text-warning me-1"></i> {{ $address->address_type }}
                                    </h6>
                                    @if($address->is_default)
                                        <span class="badge bg-success">Default Address</span>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('client.profile.addresses.destroy', $address->id) }}" onsubmit="return confirm('Are you sure you want to remove this address?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                            <hr class="text-muted opacity-25">
                            <p class="text-muted mb-0 lh-base">
                                {{ $address->address_line_1 }}
                                @if($address->address_line_2)<br>{{$address->address_line_2 }}@endif
                                @if($address->city)<br>{{$address->city }}@endif
                                @if($address->county)<br>{{$address->county }} County@endif
                                @if($address->postal_code)<br>Postal Code: {{$address->postal_code }}@endif
                                @if($address->country)<br>{{$address->country }}@endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-4">
                            <i class="fa-solid fa-map-location-dot fs-2 text-secondary opacity-50 mb-2"></i>
                            <p class="mb-0">No addresses have been added yet.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>


    {{-- ADD CONTACT MODAL --}}
    <div class="modal fade" id="addContactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('client.profile.contacts.store') }}">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Add New Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name *</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" name="last_name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Job Title</label>
                                <input type="text" name="job_title" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Alternative Phone</label>
                                <input type="text" name="alternative_phone" class="form-control">
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="form-check">
                                    <input type="hidden" name="is_primary" value="0">
                                    <input type="checkbox" name="is_primary" value="1" class="form-check-input" id="isPrimary">
                                    <label class="form-check-label fw-semibold" for="isPrimary">Make primary contact</label>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="form-check">
                                    <input type="hidden" name="receives_notifications" value="0">
                                    <input type="checkbox" name="receives_notifications" value="1" class="form-check-input" id="receivesNotifications" checked>
                                    <label class="form-check-label fw-semibold" for="receivesNotifications">Receive notifications</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-brand px-4">Save Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ADD ADDRESS MODAL --}}
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('client.profile.addresses.store') }}">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Add New Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal
