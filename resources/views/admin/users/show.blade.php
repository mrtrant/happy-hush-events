@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <a
                href="{{ route('admin.users.index') }}"
                class="text-sm font-medium text-gray-500 hover:text-gray-900"
            >
                ← Back to Users
            </a>

            <h1 class="mt-2 text-2xl font-bold text-gray-900">
                User Details
            </h1>
        </div>

        <div class="flex flex-wrap gap-3">
            <a
                href="{{ route('admin.users.edit', $user->id) }}"
                class="inline-flex justify-center rounded-lg bg-yellow-500 px-4 py-2.5 text-sm font-semibold text-gray-900 hover:bg-yellow-400"
            >
                Edit User
            </a>

            <form method="POST" action="{{ route('admin.users.send-password-reset', $user->id) }}" class="inline">
                @csrf
                <button
                    type="submit"
                    class="inline-flex justify-center rounded-lg border border-blue-300 bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50"
                    onclick="return confirm('Send password reset link to this user?')"
                >
                    Send Password Reset Link
                </button>
            </form>
        </div>

    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="rounded-xl bg-white p-6 shadow-sm lg:col-span-2">

            <h2 class="text-lg font-semibold text-gray-900">
                Account Information
            </h2>

            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-400">
                        Name
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->first_name }}
                        {{ $user->middle_name }}
                        {{ $user->last_name }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-400">
                        User Code
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->user_code }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-400">
                        Email
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->email }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-400">
                        Phone
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->phone ?: 'Not provided' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-400">
                        Status
                    </dt>
                    <dd class="mt-1">
                        @if($user->is_active)
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                Active
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                Inactive
                            </span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-400">
                        Last Login
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->last_login_at ?: 'Never' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-400">
                        Created
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->created_at }}
                    </dd>
                </div>

            </dl>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Roles
            </h2>

            <div class="mt-4 space-y-2">
                @forelse($roles as $role)
                    <div class="rounded-lg bg-gray-50 px-3 py-2">
                        <div class="font-medium text-gray-800">
                            {{ $role->name }}
                        </div>

                        @if($role->description)
                            <div class="mt-1 text-xs text-gray-500">
                                {{ $role->description }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">
                        No roles assigned.
                    </p>
                @endforelse
            </div>

        </div>

    </div>

</div>
@endsection
