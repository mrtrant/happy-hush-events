<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title') | Happy Hush Events Kenya
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Bootstrap Icons for App Navigation Bar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('styles')

    <!-- Mobile App Padding Adjustment Style -->
    <style>
        @media (max-width: 991px) {
            main {
                padding-bottom: 75px; /* Ensures content doesn't get hidden behind the floating bottom bar */
            }
        }
    </style>

</head>

<body>

@include('components.website.navbar')

<main>

    @yield('content')

</main>

@include('components.website.footer')

<!-- Mobile Bottom App Bar (Visible only on mobile screens < 992px) -->
<nav class="navbar fixed-bottom bg-white border-top d-lg-none shadow-lg py-2">
    <div class="container-fluid d-flex justify-content-around text-center">

        <!-- Home / Dashboard -->
        <a href="{{ route('home') }}" class="text-decoration-none text-dark d-flex flex-column align-items-center {{ request()->routeIs('home') ? 'text-primary fw-bold' : 'text-muted' }}">
            <i class="bi bi-house-door fs-5"></i>
            <small style="font-size: 10px;">Home</small>
        </a>

        <!-- Services -->
        <a href="{{ route('services') }}" class="text-decoration-none text-dark d-flex flex-column align-items-center {{ request()->routeIs('services') ? 'text-primary fw-bold' : 'text-muted' }}">
            <i class="bi bi-grid fs-5"></i>
            <small style="font-size: 10px;">Services</small>
        </a>

        <!-- Action Center / Quick Booking -->
        <a href="{{ route('contact') }}" class="text-decoration-none text-white d-flex align-items-center justify-content-center bg-danger rounded-circle shadow" style="width: 42px; height: 42px; margin-top: -12px;">
            <i class="bi bi-plus-lg fs-5"></i>
        </a>

        <!-- Packages -->
        <a href="{{ route('packages') }}" class="text-decoration-none text-dark d-flex flex-column align-items-center {{ request()->routeIs('packages') ? 'text-primary fw-bold' : 'text-muted' }}">
            <i class="bi bi-box-seam fs-5"></i>
            <small style="font-size: 10px;">Packages</small>
        </a>

        <!-- Contact / More -->
        <a href="{{ route('contact') }}" class="text-decoration-none text-dark d-flex flex-column align-items-center {{ request()->routeIs('contact') ? 'text-primary fw-bold' : 'text-muted' }}">
            <i class="bi bi-chat-dots fs-5"></i>
            <small style="font-size: 10px;">Contact</small>
        </a>

    </div>
</nav>

@stack('scripts')

</body>

</html>
