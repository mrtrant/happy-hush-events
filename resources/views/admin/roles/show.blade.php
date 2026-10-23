@extends('layouts.admin')

@section('title', 'Role Details')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <a
                href="{{ route('admin.roles.index') }}"
                class="text-sm font-medium text-gray-500"
            >
                ← Back to Roles
            </a>

            <h1 class="mt-2 text-2xl font-bold text-gray-900">
                {{ $roleRecord->name }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                {{ $roleRecord->description ?: 'No description' }}
            </p>
        </div>

        <a
            href="{{ route('admin.roles.edit', $roleRecord->id) }}"
            class="rounded-lg bg-yellow-500 px-4 py-2.5 text-sm font-semibold text-gray-900"
        >
            Edit Role
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Assigned Permissions
            </h2>

            <div class="mt-4 space-y-4">

                @forelse($permissions->groupBy('module') as $module => $modulePermissions)

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">
                            {{ $module }}
                        </h3>

                        <div class="mt-2 space-y-2">

                            @foreach($modulePermissions as $permission)

                                <div class="rounded-lg bg-gray-50 px-3 py-2">
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $permission->permission_key }}
                                    </div>

                                    @if($permission->description)
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $permission->description }}
                                        </div>
                                    @endif
                                </div>

                            @endforeach

                        </div>
                    </div>

                @empty

                    <p class="text-sm text-gray-500">
                        No permissions assigned.
                    </p>

                @endforelse

            </div>

        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Users With This Role
            </h2>

            <div class="mt-4 divide-y divide-gray-200">

                @forelse($users as $user)

                    <div class="py-3">

                        <div class="font-medium text-gray-800">
                            {{ $user->first_name }}
                            {{ $user->last_name }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $user->email }}
                        </div>

                        <div class="mt-1">
                            @if($user->is_active)
                                <span class="text-xs font-semibold text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="text-xs font-semibold text-gray-500">
                                    Inactive
                                </span>
                            @endif
                        </div>

                    </div>

                @empty

                    <p class="py-4 text-sm text-gray-500">
                        No users are assigned to this role.
                    </p>

                @endforelse

            </div>

        </div>

    </div>

</div>
@endsection
