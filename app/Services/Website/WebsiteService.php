<?php

namespace App\Services\Website;

use App\Models\Booking;
use App\Models\BookingEvent;
use App\Models\ClientProfile;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\Service;
use App\Models\StaffProfile;
use App\Models\Testimonial;

class WebsiteService
{
    /**
     * Homepage Data
     */
    public function homepage(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Featured Services
            |--------------------------------------------------------------------------
            */

            'services' => Service::with('category')
                ->where('is_active', true)
                ->orderBy('name')
                ->take(6)
                ->get(),

            /*
            |--------------------------------------------------------------------------
            | Packages
            |--------------------------------------------------------------------------
            */

            'packages' => Package::where('is_active', true)
                ->orderBy('package_price')
                ->take(3)
                ->get(),

            /*
            |--------------------------------------------------------------------------
            | Gallery
            |--------------------------------------------------------------------------
            */

            'gallery' => Gallery::where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->take(8)
                ->get(),

            /*
            |--------------------------------------------------------------------------
            | Testimonials
            |--------------------------------------------------------------------------
            */

            'testimonials' => Testimonial::where('is_approved', true)
                ->latest()
                ->take(6)
                ->get(),

            /*
            |--------------------------------------------------------------------------
            | Recent Events
            |--------------------------------------------------------------------------
            */

            'featuredEvents' => BookingEvent::with([
                    'booking',
                    'eventType',
                    'venue'
                ])
                ->latest('event_date')
                ->take(3)
                ->get(),

            /*
            |--------------------------------------------------------------------------
            | Website Statistics
            |--------------------------------------------------------------------------
            */

            'statistics' => [

                'bookings' => Booking::count(),

                'clients' => ClientProfile::count(),

                'staff' => StaffProfile::count(),

                'services' => Service::count(),

            ],

        ];
    }

    /**
     * Services Page
     */
    public function services()
    {
        return Service::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(12);
    }

    /**
     * Packages Page
     */
    public function packages()
    {
        return Package::where('is_active', true)
            ->orderBy('package_price')
            ->paginate(12);
    }

    /**
     * Gallery Page
     */
    public function gallery()
    {
        return Gallery::with('category')
            ->where('is_active', true)
            ->latest()
            ->paginate(18);
    }

    /**
     * Testimonials
     */
    public function testimonials()
    {
        return Testimonial::where('is_approved', true)
            ->latest()
            ->paginate(10);
    }

    /**
     * Featured Events
     */
    public function events()
    {
        return BookingEvent::with([
                'booking',
                'eventType',
                'venue'
            ])
            ->latest('event_date')
            ->paginate(12);
    }
}
