@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    <div>
        <a
            href="{{ route('admin.roles.show', $roleRecord->id) }}"
            class="text-sm font-medium text-gray-500"
        >
            ← Back to Role
        </a>

        <h1 class="mt-2 text-2xl font-bold text-gray-900">
            Edit Role
        </h1>
    </div>

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.roles.update', $roleRecord->id) }}"
        class="rounded-xl bg-white p-6 shadow-sm"
    >
        @csrf
        @method('PUT')

        @include('admin.roles.partials.form')

        <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 pt-6">

            <a
                href="{{ route('admin.roles.show', $roleRecord->id) }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-yellow-500 px-5 py-2.5 text-sm font-semibold text-gray-900"
            >
                Save Changes
            </button>

        </div>
    </form>

</div>
@endsection
