@extends('layouts.guest')

@section('title', 'Client Registration')

@section('content')

<div class="container-fluid min-vh-100 p-0">

    <div class="row g-0 min-vh-100">

        <!-- Left Branding Panel with Blurred Background Image & Dark Overlay -->
        <div class="col-lg-5 d-none d-lg-flex align-items-center justify-content-center position-relative overflow-hidden">

            <!-- Background Image Layer with Blur Effect -->
            <div class="position-absolute top-0 start-0 w-100 h-100"
                 style="background-image: url('{{ asset('assets/images/logo/logo-bg.png') }}'); background-size: cover; background-position: center; filter: blur(6px); transform: scale(1.05);">
            </div>

            <!-- Dark Overlay for Contrast -->
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>

            <!-- Foreground Content -->
            <div class="position-relative text-center text-white px-5 z-1">

                <img
                    src="{{ asset('assets/images/logo/logo-white.png') }}"
                    class="img-fluid mb-4"
                    style="max-width:220px;"
                    alt="Happy Hush">

                <h2 class="fw-bold">
                    Happy Hush Events Kenya
                </h2>

                <p class="lead mt-3">
                    Create your client account and begin planning your unforgettable event.
                </p>

            </div>

        </div>

        <!-- Registration Form Panel -->
        <div class="col-lg-7 d-flex align-items-center bg-white">

            <div class="w-100 p-5">

                <div class="mx-auto" style="max-width:700px;">

                    <h2 class="fw-bold mb-2">
                        Client Registration
                    </h2>

                    <p class="text-muted mb-4">
                        Complete the form below to create your account.
                    </p>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('client.register.store') }}">

                        @csrf

                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name</label>
                                <input
                                    type="text"
                                    name="first_name"
                                    class="form-control"
                                    value="{{ old('first_name') }}"
                                    required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input
                                    type="text"
                                    name="middle_name"
                                    class="form-control"
                                    value="{{ old('middle_name') }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name</label>
                                <input
                                    type="text"
                                    name="last_name"
                                    class="form-control"
                                    value="{{ old('last_name') }}"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    value="{{ old('phone') }}"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Client Type</label>
                                <select
                                    name="client_type"
                                    id="client_type"
                                    class="form-select"
                                    required>
                                    <option value="Individual">Individual</option>
                                    <option value="Company">Company</option>
                                    <option value="Organization">Organization</option>
                                    <option value="School">School</option>
                                    <option value="Church">Church</option>
                                    <option value="Government">Government</option>
                                </select>
                            </div>

                            <div
                                class="col-md-6 mb-3"
                                id="organization_section">
                                <label class="form-label">Organization Name</label>
                                <input
                                    type="text"
                                    name="organization_name"
                                    class="form-control"
                                    value="{{ old('organization_name') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">KRA PIN</label>
                                <input
                                    type="text"
                                    name="kra_pin"
                                    class="form-control"
                                    value="{{ old('kra_pin') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website</label>
                                <input
                                    type="url"
                                    name="website"
                                    class="form-control"
                                    value="{{ old('website') }}">
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Preferred Contact</label>
                                <select
                                    name="preferred_contact_method"
                                    class="form-select">
                                    <option>Email</option>
                                    <option selected>Phone</option>
                                    <option>SMS</option>
                                    <option>WhatsApp</option>
                                </select>
                            </div>

                        </div>

                        <button
                            class="btn btn-dark btn-lg w-100">
                            Create Account
                        </button>

                    </form>

                    <div class="text-center mt-4">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                            Login
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const clientType = document.getElementById('client_type');
    const organization = document.getElementById('organization_section');

    function toggleOrganization() {
        if (clientType.value === 'Individual') {
            organization.style.display = 'none';
        } else {
            organization.style.display = 'block';
        }
    }

    toggleOrganization();
    clientType.addEventListener('change', toggleOrganization);
});
</script>

@endsection
