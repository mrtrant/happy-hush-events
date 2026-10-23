<div class="space-y-6">

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Role Name *
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $roleRecord->name ?? '') }}"
            maxlength="50"
            required
            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
        >

        @error('name')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Description
        </label>

        <textarea
            name="description"
            rows="3"
            maxlength="255"
            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
        >{{ old('description', $roleRecord->description ?? '') }}</textarea>

        @error('description')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Permissions
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Select the actions this role is allowed to perform.
                </p>
            </div>
        </div>

        @php
            $selectedPermissions = old(
                'permissions',
                $assignedPermissions ?? []
            );
        @endphp

        <div class="mt-4 space-y-5">

            @foreach($permissions->groupBy('module') as $module => $modulePermissions)

                <div class="rounded-xl border border-gray-200">

                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="font-semibold text-gray-800">
                            {{ $module }}
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 gap-3 p-4 md:grid-cols-2 lg:grid-cols-3">

                        @foreach($modulePermissions as $permission)

                            <label class="flex cursor-pointer gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">

                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    @checked(
                                        in_array(
                                            (int) $permission->id,
                                            array_map(
                                                'intval',
                                                $selectedPermissions
                                            ),
                                            true
                                        )
                                    )
                                    class="mt-1 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"
                                >

                                <div>
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $permission->permission_key }}
                                    </div>

                                    @if($permission->description)
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $permission->description }}
                                        </div>
                                    @endif
                                </div>

                            </label>

                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>

        @error('permissions')
            <p class="mt-2 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>

    <div class="flex items-center gap-3">

        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked(
                old(
                    'is_active',
                    isset($roleRecord)
                        ? $roleRecord->is_active
                        : true
                )
            )
            class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"
        >

        <span class="text-sm text-gray-700">
            Role is active
        </span>

    </div>

</div>
