@extends('layouts.website')

@section('title', 'Happy Hush Events Kenya - Unforgettable Celebrations & Planning')

@section('content')
<main>
    <section class="hero-section text-white text-center py-5" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('images/hero-bg.jpg') }}') center/cover no-repeat; min-height: 80vh; display: flex; align-items: center;">
        <div class="container py-5">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-3 text-uppercase fw-bold">Premier Event Planners in Kenya</span>
            <h1 class="display-3 fw-bold mb-4">Crafting Memories That Last a Lifetime</h1>
            <p class="lead mb-5 col-lg-8 mx-auto">From corporate galas and lavish weddings to children's parties and decor setups, Happy Hush brings your vision to life with style and precision.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('services') }}" class="btn btn-warning btn-lg px-4 fw-bold text-dark">Our Services</a>
                <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg px-4">Book a Consultation</a>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-primary fw-bold text-uppercase tracking-wider small">What We Do</span>
                <h2 class="fw-bold display-6">Exceptional Event Services</h2>
            </div>
            <div class="row g-4">
                @forelse($services ?? [] as $service)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-3">
                            <div class="card-body">
                                <h3 class="h5 fw-bold text-dark mb-3">{{ $service->name }}</h3>
                                <p class="text-muted small mb-3">{{ Str::limit($service->description, 100) }}</p>
                                <a href="{{ route('services') }}" class="text-decoration-none fw-bold small text-primary">Learn More &rarr;</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-3">
                            <div class="card-body">
                                <h3 class="h5 fw-bold text-dark mb-3">Event Planning & Coordination</h3>
                                <p class="text-muted small">Full-service planning and execution tailored to your budget and precise requirements.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-3">
                            <div class="card-body">
                                <h3 class="h5 fw-bold text-dark mb-3">Exquisite Decor & Styling</h3>
                                <p class="text-muted small">Transforming ordinary spaces into breathtaking venues with bespoke thematic setups.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-3">
                            <div class="card-body">
                                <h3 class="h5 fw-bold text-dark mb-3">Catering & Entertainment</h3>
                                <p class="text-muted small">Delightful culinary experiences, professional MCs, DJs, and entertainment for all guests.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <span class="text-primary fw-bold text-uppercase tracking-wider small">Our Portfolio</span>
                    <h2 class="fw-bold display-6 mb-0">Recent Memories</h2>
                </div>
                <a href="{{ route('gallery') }}" class="btn btn-outline-dark btn-sm">View Full Gallery</a>
            </div>
            <div class="row g-4">
                @forelse($featuredGallery ?? [] as $photo)
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm overflow-hidden h-100" style="height: 250px;">
                            <img src="{{ asset('storage/' . $photo->image_path) }}" class="w-100 h-100 object-fit-cover" alt="{{ $photo->title }}">
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">
                        <p>Explore our full gallery page for recent event photos!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-primary fw-bold text-uppercase tracking-wider small">Client Feedback</span>
                <h2 class="fw-bold display-6">What Our Clients Say</h2>
            </div>
            <div class="row g-4">
                @forelse($testimonials ?? [] as $t)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-4">
                            <div class="text-warning mb-2">
                                @for($i = 0; $i < ($t->rating ?? 5); $i++) &#9733; @endfor
                            </div>
                            <p class="text-muted fst-italic mb-3">"{{ $t->message }}"</p>
                            <h6 class="fw-bold text-dark mb-0">— {{ $t->name }}</h6>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">
                        <p>No testimonials published yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</main>
@endsection
