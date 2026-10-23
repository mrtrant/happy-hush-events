@extends('layouts.admin')

@section('title', 'Roles')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Roles
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Define roles and control their permissions.
            </p>
        </div>

        <a
            href="{{ route('admin.roles.create') }}"
            class="inline-flex justify-center rounded-lg bg-yellow-500 px-4 py-2.5 text-sm font-semibold text-gray-900 hover:bg-yellow-400"
        >
            + Add Role
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

        <form
            method="GET"
            action="{{ route('admin.roles.index') }}"
            class="flex flex-col gap-3 md:flex-row"
        >
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search role..."
                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
            >

            <select
                name="status"
                class="rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
            >
                <option value="">All statuses</option>
                <option value="active" @selected($status === 'active')>
                    Active
                </option>
                <option value="inactive" @selected($status === 'inactive')>
                    Inactive
                </option>
            </select>

            <button
                type="submit"
                class="rounded-lg bg-gray-900 px-5 py-2 text-sm font-semibold text-white"
            >
                Filter
            </button>

            <a
                href="{{ route('admin.roles.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-2 text-center text-sm font-semibold text-gray-700"
            >
                Reset
            </a>
        </form>

    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Role
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Users
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Permissions
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($roles as $role)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $role->name }}
                                </div>

                                <div class="mt-1 text-sm text-gray-500">
                                    {{ $role->description ?: 'No description' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $role->user_count }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $role->permission_count }}
                            </td>

                            <td class="px-6 py-4">
                                @if($role->is_active)
                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">

                                    <a
                                        href="{{ route('admin.roles.show', $role->id) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ route('admin.roles.edit', $role->id) }}"
                                        class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.roles.toggle-status', $role->id) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="rounded-lg border border-yellow-300 px-3 py-1.5 text-xs font-semibold text-yellow-700"
                                        >
                                            {{ $role->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    @if((int) $role->user_count === 0)
                                        <form
                                            method="POST"
                                            action="{{ route('admin.roles.destroy', $role->id) }}"
                                            onsubmit="return confirm('Delete this role?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700"
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
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                No roles found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        <div class="border-t border-gray-200 px-6 py-4">
            {{ $roles->links() }}
        </div>

    </div>

</div>
@endsection
