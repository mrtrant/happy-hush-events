<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Dashboard') - Happy Hush Events</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-orange: #c84b15;
            --brand-orange-hover: #a53c10;
            --brand-yellow: #ffc107;
            --brand-dark: #212529;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand img {
            max-height: 40px;
        }
        .btn-brand {
            background-color: var(--brand-orange);
            color: #fff;
            border: none;
        }
        .btn-brand:hover {
            background-color: var(--brand-orange-hover);
            color: #fff;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            font-weight: 500;
            border-radius: 8px;
            margin: 4px 10px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--brand-orange);
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }

        /* Mobile spacing to accommodate fixed bottom bar */
        @media (max-width: 767px) {
            main {
                padding-bottom: 80px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="{{ route('home') }}">
                <span style="color: var(--brand-orange);">Happy Hush</span>&nbsp;Events
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clientNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="clientNavbar">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user-circle text-secondary me-1"></i> {{ Auth::user()->first_name ?? Auth::user()->name ?? 'Client' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li>
                                <a class="dropdown-item" href="{{ route('client.dashboard') }}">
                                    <i class="fa-solid fa-chart-line me-2"></i> Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation (Desktop & Tablet) -->
            <nav class="col-md-3 col-lg-2 d-none d-md-block sidebar collapse pt-3">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}" href="{{ route('client.dashboard') }}">
                                <i class="fa-solid fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.bookings*') ? 'active' : '' }}" href="{{ route('client.bookings.index') }}">
                                <i class="fa-solid fa-calendar-check"></i> My Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.quotations*') ? 'active' : '' }}" href="{{ route('client.quotations.index') }}">
                                <i class="fa-solid fa-file-invoice"></i> Quotations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.invoices*') ? 'active' : '' }}" href="{{ route('client.invoices.index') }}">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.payments*') ? 'active' : '' }}" href="{{ route('client.payments.index') }}">
                                <i class="fa-solid fa-receipt"></i> Payments
                            </a>
                        </li>
                        <li class="nav-item mt-3 border-top pt-2">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="fa-solid fa-globe"></i> Main Website
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar (Visible on mobile screens < 768px) -->
    <nav class="navbar fixed-bottom bg-white border-top d-md-none shadow-lg py-2">
        <div class="container-fluid d-flex justify-content-around text-center">

            <a href="{{ route('client.dashboard') }}" class="text-decoration-none d-flex flex-column align-items-center {{ request()->routeIs('client.dashboard') ? 'text-danger fw-bold' : 'text-secondary' }}">
                <i class="bi bi-speedometer2 fs-5"></i>
                <small style="font-size: 10px;">Dashboard</small>
            </a>

            <a href="{{ route('client.bookings.index') }}" class="text-decoration-none d-flex flex-column align-items-center {{ request()->routeIs('client.bookings*') ? 'text-danger fw-bold' : 'text-secondary' }}">
                <i class="bi bi-calendar-check fs-5"></i>
                <small style="font-size: 10px;">Bookings</small>
            </a>

            <a href="{{ route('client.bookings.create') }}" class="text-decoration-none text-white d-flex align-items-center justify-content-center bg-danger rounded-circle shadow" style="width: 40px; height: 40px; margin-top: -12px;">
                <i class="bi bi-plus-lg fs-5"></i>
            </a>

            <a href="{{ route('client.invoices.index') }}" class="text-decoration-none d-flex flex-column align-items-center {{ request()->routeIs('client.invoices*') ? 'text-danger fw-bold' : 'text-secondary' }}">
                <i class="bi bi-receipt fs-5"></i>
                <small style="font-size: 10px;">Invoices</small>
            </a>

            <a href="{{ route('client.payments.index') }}" class="text-decoration-none d-flex flex-column align-items-center {{ request()->routeIs('client.payments*') ? 'text-danger fw-bold' : 'text-secondary' }}">
                <i class="bi bi-credit-card fs-5"></i>
                <small style="font-size: 10px;">Payments</small>
            </a>

        </div>
    </nav>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
