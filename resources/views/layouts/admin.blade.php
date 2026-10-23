<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'Admin Dashboard') - Happy Hush Events Kenya
    </title>

    @vite(['resources/css/admin.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-900">

<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white min-h-screen hidden lg:block flex-shrink-0">

        <div class="px-6 py-5 border-b border-gray-800">
            <a href="{{ route('admin.dashboard') }}"
               class="text-xl font-bold">
                Happy Hush
            </a>

            <div class="text-xs text-gray-400 mt-1">
                Administration
            </div>
        </div>

        <nav class="p-4 space-y-1">

            <a href="{{ route('admin.dashboard') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Dashboard
            </a>

            <div class="pt-4 pb-2 px-4 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                Management
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Users
            </a>

            <a href="{{ route('admin.roles.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Roles
            </a>

            <a href="{{ route('admin.permissions.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.permissions.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Permissions
            </a>

            <a href="{{ route('admin.clients.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.clients.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Clients
            </a>

            <a href="{{ route('admin.bookings.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.bookings.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Bookings
            </a>

            <a href="{{ route('admin.events.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.events.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Events
            </a>

            <div class="pt-4 pb-2 px-4 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                Sales
            </div>

            <a href="{{ route('admin.quotations.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.quotations.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Quotations
            </a>

            <a href="{{ route('admin.invoices.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.invoices.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Invoices
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.payments.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Payments
            </a>

            <div class="pt-4 pb-2 px-4 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                Operations
            </div>

            <a href="{{ route('admin.event-types.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.event-types.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Event Types
            </a>

            <a href="{{ route('admin.packages.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.packages.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Packages
            </a>

            <a href="{{ route('admin.services.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.services.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Services
            </a>

            <a href="#"
               class="block px-4 py-3 rounded-lg transition-colors hover:bg-gray-800">
                Staff
            </a>

            <a href="{{ route('admin.inventory.items.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.inventory.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Inventory
            </a>

            <div class="pt-4 pb-2 px-4 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                Website
            </div>

            <a href="{{ route('admin.gallery.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.gallery.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Gallery
            </a>

            <a href="{{ route('admin.testimonials.index') }}"
               class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.testimonials.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                Testimonials
            </a>

            <a href="{{ route('admin.faqs.index') }}"
                class="block px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.faqs.*') ? 'bg-indigo-600 font-semibold' : 'hover:bg-gray-800' }}">
                    FAQs
            </a>

        </nav>
    </aside>

    {{-- Main Content Wrapper --}}
    <div class="flex-1 min-w-0 flex flex-col">

        <header class="bg-white border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">
                        @yield('page-title', 'Dashboard')
                    </h1>
                    <p class="text-sm text-gray-500">
                        Happy Hush Events Kenya
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-medium text-gray-900">
                            {{ auth()->user()->first_name ?? auth()->user()->name }}
                            {{ auth()->user()->last_name ?? '' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            Administrator
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm text-red-600 hover:text-red-800 font-medium transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-6 flex-1">
            @yield('content')
        </main>

    </div>

</div>

@stack('scripts')

</body>
</html>
