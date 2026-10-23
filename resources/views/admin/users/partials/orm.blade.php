<div class="space-y-6">

    <div>
        <h2 class="text-lg font-semibold text-gray-900">
            Personal Information
        </h2>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    First Name *
                </label>

                <input
                    type="text"
                    name="first_name"
                    value="{{ old('first_name', $user->first_name ?? '') }}"
                    required
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >

                @error('first_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Middle Name
                </label>

                <input
                    type="text"
                    name="middle_name"
                    value="{{ old('middle_name', $user->middle_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >

                @error('middle_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Last Name *
                </label>

                <input
                    type="text"
                    name="last_name"
                    value="{{ old('last_name', $user->last_name ?? '') }}"
                    required
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >

                @error('last_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">
            Contact Information
        </h2>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Email *
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email ?? '') }}"
                    required
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >

                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Phone
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $user->phone ?? '') }}"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >

                @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">
            Security
        </h2>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Password {{ isset($user) ? '' : '*' }}
                </label>

                <input
                    type="password"
                    name="password"
                    {{ isset($user) ? '' : 'required' }}
                    autocomplete="new-password"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >

                @if(isset($user))
                    <p class="mt-1 text-xs text-gray-500">
                        Leave blank to keep the current password.
                    </p>
                @endif

                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Confirm Password {{ isset($user) ? '' : '*' }}
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    {{ isset($user) ? '' : 'required' }}
                    autocomplete="new-password"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                >
            </div>

        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">
            Roles
        </h2>

        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">

            @foreach($roles as $role)
                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">

                    <input
                        type="checkbox"
                        name="roles[]"
                        value="{{ $role->id }}"
                        @checked(
                            in_array(
                                (int) $role->id,
                                old(
                                    'roles',
                                    $assignedRoles ?? []
                                )
                            )
                        )
                        class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"
                    >

                    <div>
                        <div class="text-sm font-semibold text-gray-800">
                            {{ $role->name }}
                        </div>

                        @if($role->description)
                            <div class="text-xs text-gray-500">
                                {{ $role->description }}
                            </div>
                        @endif
                    </div>

                </label>
            @endforeach

        </div>

        @error('roles')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
        @enderror

        @error('roles.*')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">
            Account

        </h2>

        <div class="mt-4 flex items-center gap-3">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old(
                    'is_active',
                    isset($user) ? $user->is_active : true
                ))
                class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"
            >

            <span class="text-sm text-gray-700">
                Account is active
            </span>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">
            Profile Photo
        </h2>

        <div class="mt-4">
            <input
                type="file"
                name="profile_photo"
                accept=".jpg,.jpeg,.png,.webp"
                class="block w-full text-sm text-gray-600"
            >

            <p class="mt-1 text-xs text-gray-500">
                JPG, PNG or WEBP. Maximum 2 MB.
            </p>

            @error('profile_photo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

</div>
