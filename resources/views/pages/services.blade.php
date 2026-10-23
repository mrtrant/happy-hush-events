@extends('layouts.website')

@section('title', 'Our Services - Happy Hush Events Kenya')

@section('content')
<main>
    <section class="py-5 bg-light text-center">
        <div class="container py-4">
            <span class="badge bg-primary text-white mb-2 px-3 py-2 rounded-pill">WHAT WE OFFER</span>
            <h1 class="fw-bold display-5">Professional Event Services</h1>
            <p class="text-muted lead">Comprehensive solutions tailored to bring your next event to absolute perfection.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="row g-4">
                @forelse($services ?? [] as $service)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100 p-4">
                            <div class="card-body">
                                <h3 class="h4 fw-bold text-dark mb-3">{{ $service->name }}</h3>
                                <p class="text-muted mb-4">{{ $service->description }}</p>
                                @if(isset($service->price))
                                    <div class="text-primary fw-bold mb-3">Starts from KES {{ number_format($service->price, 2) }}</div>
                                @endif
                                <a href="{{ route('contact') }}" class="btn btn-outline-dark btn-sm w-100">Inquire Now</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Services list is being updated. Contact us directly for customized service packages!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</main>
@endsection
