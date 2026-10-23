@extends('layouts.guest')

@section('title', 'Login')

@section('content')

<div class="container-fluid min-vh-100 p-0">

    <div class="row g-0 min-vh-100">

        <!-- Left Panel with Blurred Background Image & Dark Overlay -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center position-relative overflow-hidden">

            <!-- Background Image Layer -->
            <div class="position-absolute top-0 start-0 w-100 h-100"
                 style="background-image: url('{{ asset('assets/images/logo/logo-bg.png') }}'); background-size: cover; background-position: center; filter: blur(6px); transform: scale(1.05);">
            </div>

            <!-- Dark Overlay for Contrast -->
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>

            <!-- Foreground Content -->
            <div class="position-relative text-center text-white px-5 z-1">

                <!-- Using logo-white.png for maximum legibility on dark backgrounds -->
                <img
                    src="{{ asset('assets/images/logo/logo-white.png') }}"
                    alt="Happy Hush Events"
                    class="img-fluid mb-4"
                    style="max-width:220px;">

                <h1 class="fw-bold mb-4">
                    Happy Hush Events Kenya
                </h1>

                <p class="lead">
                    Creating unforgettable experiences through creativity,
                    professionalism and exceptional event management.
                </p>

            </div>

        </div>

        <!-- Right Panel (Form) -->
        <div class="col-lg-6 d-flex align-items-center bg-white">

            <div class="w-100 px-4 px-md-5">

                <div class="mx-auto" style="max-width:480px;">

                    <h2 class="fw-bold mb-2">
                        Welcome Back
                    </h2>

                    <p class="text-muted mb-4">
                        Sign in to continue.
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control form-control-lg"
                                value="{{ old('email') }}"
                                required
                                autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control form-control-lg"
                                required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="remember"
                                    value="1">
                                <label class="form-check-label">Remember Me</label>
                            </div>

                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                Forgot Password?
                            </a>
                        </div>

                        <button class="btn btn-dark btn-lg w-100">
                            Login
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <span>Don't have an account?</span>
                        <a href="{{ route('client.register') }}" class="fw-semibold text-decoration-none">
                            Register Here
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            ← Return to Website
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
