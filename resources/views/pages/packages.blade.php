@extends('layouts.website')

@section('title', 'Event Packages & Pricing - Happy Hush Events Kenya')

@section('content')
<main>
    <section class="py-5 bg-light text-center">
        <div class="container py-4">
            <span class="badge bg-primary text-white mb-2 px-3 py-2 rounded-pill">TRANSPARENT PRICING</span>
            <h1 class="fw-bold display-5">Curated Event Packages</h1>
            <p class="text-muted lead">Choose from our pre-designed event bundles or request a custom quote.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="row g-4">
                @forelse($packages ?? [] as $pkg)
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100 p-4">
                            <div class="card-body">
                                <h3 class="h4 fw-bold text-dark mb-2">{{ $pkg->name }}</h3>
                                <p class="text-muted small mb-4">{{ $pkg->description }}</p>
                                <div class="fs-3 fw-bold text-primary mb-4">KES {{ number_format($pkg->price ?? 0, 2) }}</div>
                                <a href="{{ route('contact') }}" class="btn btn-dark w-100">Book This Package</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-4">
                            <div class="card-body">
                                <h3 class="h4 fw-bold text-dark mb-2">Standard Celebration Package</h3>
                                <p class="text-muted small mb-4">Ideal for birthdays, baby showers, and intimate family gatherings.</p>
                                <div class="fs-3 fw-bold text-primary mb-4">Custom Quote</div>
                                <a href="{{ route('contact') }}" class="btn btn-dark w-100">Inquire Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-4 border-primary">
                            <div class="card-body">
                                <span class="badge bg-warning text-dark mb-2">Most Popular</span>
                                <h3 class="h4 fw-bold text-dark mb-2">Executive Corporate Package</h3>
                                <p class="text-muted small mb-4">Comprehensive planning, full decor styling, sound system, and coordination.</p>
                                <div class="fs-3 fw-bold text-primary mb-4">Custom Quote</div>
                                <a href="{{ route('contact') }}" class="btn btn-primary w-100">Inquire Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-4">
                            <div class="card-body">
                                <h3 class="h4 fw-bold text-dark mb-2">Grand Wedding Package</h3>
                                <p class="text-muted small mb-4">End-to-end luxury wedding planning, premium decor, inventory, and full-day management.</p>
                                <div class="fs-3 fw-bold text-primary mb-4">Custom Quote</div>
                                <a href="{{ route('contact') }}" class="btn btn-dark w-100">Inquire Now</a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</main>
@endsection
