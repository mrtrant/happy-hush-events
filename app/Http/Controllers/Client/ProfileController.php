<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientAddressRequest;
use App\Http\Requests\Client\StoreClientContactRequest;
use App\Http\Requests\Client\UpdateClientAddressRequest;
use App\Http\Requests\Client\UpdateClientContactRequest;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Services\Client\ProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService
    ) {
    }

    public function index(): View
    {
        $profile = $this->profileService->profile(
            (int) Auth::id()
        );

        return view(
            'client.profile.index',
            compact('profile')
        );
    }

    public function update(
        UpdateProfileRequest $request
    ): RedirectResponse {
        $profile = $this->profileService->profile(
            (int) Auth::id()
        );

        $this->profileService->updateProfile(
            $profile,
            $request->validated()
        );

        return back()->with(
            'success',
            'Your profile information has been updated successfully.'
        );
    }

    public function storeContact(
        StoreClientContactRequest $request
    ): RedirectResponse {
        $profile = $this->profileService->profile(
            (int) Auth::id()
        );

        $this->profileService->createContact(
            $profile,
            $request->validated()
        );

        return back()->with(
            'success',
            'Contact added successfully.'
        );
    }

    public function updateContact(
        UpdateClientContactRequest $request,
        int $contact
    ): RedirectResponse {
        $profile = $this->profileService->profile(
            (int) Auth::id()
        );

        $this->profileService->updateContact(
            $profile,
            $contact,
            $request->validated()
        );

        return back()->with(
            'success',
            'Contact updated successfully.'
        );
    }

    public function destroyContact(
        int $contact
    ): RedirectResponse {
        $profile = $this->profileService->profile(
            (int) Auth::id()
        );

        $this->profileService->deleteContact(
            $profile,
            $contact
        );

        return back()->with(
            'success',
            'Contact removed successfully.'
        );
    }

    public function storeAddress(
        StoreClientAddressRequest $request
    ): RedirectResponse {
        $profile = $this->profileService->profile(
            (int) Auth::id()
        );

        $this->profileService->createAddress(
            $profile,
            $request->validated()
        );

        return back()->with(
            'success',
            'Address added successfully.'
        );
    }

    public function updateAddress(
        UpdateClientAddressRequest $request,
        int $address
    ): RedirectResponse {
        $profile = $this->profileService->profile(
            (int) Auth::id()
        );

        $this->profileService->updateAddress(
            $profile,
            $address,
            $request->validated()
        );

        return back()->with(
            'success',
            'Address updated successfully.'
        );
    }

    public function destroyAddress(
        int $address
    ): RedirectResponse {
        $profile = $this->profileService->profile(
            (int) Auth::id()
        );

        $this->profileService->deleteAddress(
            $profile,
            $address
        );

        return back()->with(
            'success',
            'Address removed successfully.'
        );
    }
}
