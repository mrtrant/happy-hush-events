<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">

        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center"
            href="{{ route('home') }}">
            <img
                src="{{ asset('assets/images/logo/logo.png') }}"
                alt="Happy Hush Events Kenya"
                class="brand-logo">
        </a>

        {{-- Mobile menu button --}}
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Navigation links --}}
        <div class="collapse navbar-collapse" id="mainNavbar">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                       href="{{ route('home') }}">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                       href="{{ route('about') }}">
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}"
                       href="{{ route('services') }}">
                        Services
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('packages') ? 'active' : '' }}"
                       href="{{ route('packages') }}">
                        Packages
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}"
                       href="{{ route('gallery') }}">
                        Gallery
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                       href="{{ route('home') }}#testimonials">
                        Testimonials
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                       href="{{ route('contact') }}">
                        Contact
                    </a>
                </li>

            </ul>

            {{-- Login / Register / Dashboard with unique modifier classes --}}
            <div class="d-flex align-items-center flex-wrap gap-2">

                @guest
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="btn btn-outline-brand navbar-btn-login">
                            Login
                        </a>
                    @endif

                    @if (Route::has('client.register'))
                        <a href="{{ route('client.register') }}"
                           class="btn btn-brand navbar-btn-register">
                            Register
                        </a>
                    @endif
                @else
                    @php
                        $dashboardRoute = route('client.dashboard');
                        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
                            $dashboardRoute = route('admin.dashboard');
                        } elseif (auth()->user()->hasRole('Staff')) {
                            $dashboardRoute = route('staff.dashboard');
                        }
                    @endphp

                    <a href="{{ $dashboardRoute }}"
                       class="btn btn-brand navbar-btn-dashboard">
                        Dashboard
                    </a>
                @endguest

            </div>

        </div>
    </div>
</nav>
