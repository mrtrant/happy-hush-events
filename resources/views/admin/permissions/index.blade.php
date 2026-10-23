@extends('layouts.admin')

@section('title', 'Permissions')

@section('content')
<div class="space-y-6">

    <div>
        <a
            href="{{ route('admin.roles.index') }}"
            class="text-sm font-medium text-gray-500"
        >
            ← Back to Roles
        </a>

        <h1 class="mt-2 text-2xl font-bold text-gray-900">
            Permissions
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Available system capabilities that can be assigned to roles.
        </p>
    </div>

    <div class="rounded-xl bg-white p-4 shadow-sm">

        <form
            method="GET"
            action="{{ route('admin.permissions.index') }}"
            class="flex flex-col gap-3 md:flex-row"
        >

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search permissions..."
                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
            >

            <select
                name="module"
                class="rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
            >
                <option value="">All modules</option>

                @foreach($modules as $item)
                    <option
                        value="{{ $item }}"
                        @selected($module === $item)
                    >
                        {{ $item }}
                    </option>
                @endforeach
            </select>

            <button
                type="submit"
                class="rounded-lg bg-gray-900 px-5 py-2 text-sm font-semibold text-white"
            >
                Filter
            </button>

            <a
                href="{{ route('admin.permissions.index') }}"
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
                            Module
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Permission
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Description
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                            Roles
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($permissions as $permission)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                    {{ $permission->module }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <code class="text-sm font-medium text-gray-800">
                                    {{ $permission->permission_key }}
                                </code>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $permission->description ?: '—' }}
                            </td>

                            <td class="px-6 py-4 text-right text-sm text-gray-700">
                                {{ $permission->role_count }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                No permissions found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="border-t border-gray-200 px-6 py-4">
            {{ $permissions->links() }}
        </div>

    </div>

</div>
@endsection
