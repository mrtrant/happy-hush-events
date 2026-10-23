@extends('layouts.admin')

@section('title', 'Users & Access')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Users & Access
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage system users, roles and account status.
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center justify-center rounded-lg bg-yellow-500 px-4 py-2.5 text-sm font-semibold text-gray-900 hover:bg-yellow-400">
            + Add User
        </a>
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

    <div class="rounded-xl bg-white p-4 shadow-sm">
        <form method="GET"
              action="{{ route('admin.users.index') }}"
              class="grid grid-cols-1 gap-3 md:grid-cols-4">

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Search
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Name, email, phone or user code"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >
                    <option value="">All statuses</option>
                    <option value="active" @selected($status === 'active')>
                        Active
                    </option>
                    <option value="inactive" @selected($status === 'inactive')>
                        Inactive
                    </option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Role
                </label>

                <select
                    name="role"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >
                    <option value="">All roles</option>

                    @foreach($roles as $item)
                        <option value="{{ $item->id }}"
                            @selected((string) $role === (string) $item->id)>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button
                    type="submit"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800"
                >
                    Filter
                </button>

                <a
                    href="{{ route('admin.users.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            User
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            User Code
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Roles
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $user->first_name }}
                                    {{ $user->middle_name }}
                                    {{ $user->last_name }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    {{ $user->email }}
                                </div>

                                @if($user->phone)
                                    <div class="text-xs text-gray-400">
                                        {{ $user->phone }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->user_code }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->role_names ?: 'No role assigned' }}
                            </td>

                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">

                                    <a
                                        href="{{ route('admin.users.show', $user->id) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ route('admin.users.edit', $user->id) }}"
                                        class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.toggle-status', $user->id) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="rounded-lg border border-yellow-300 px-3 py-1.5 text-xs font-semibold text-yellow-700 hover:bg-yellow-50"
                                        >
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    @if($user->id !== auth()->id())
                                        <form
                                            method="POST"
                                            action="{{ route('admin.users.destroy', $user->id) }}"
                                            onsubmit="return confirm('Delete this user account?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <p class="font-medium text-gray-700">
                                    No users found.
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    Try changing your search or filters.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
