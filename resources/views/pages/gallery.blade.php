@extends('layouts.website')

@section('title', 'Our Gallery - Happy Hush Events Kenya')

@section('content')
<main>
    <section class="py-5 bg-light text-center">
        <div class="container py-4">
            <span class="badge bg-primary text-white mb-2 px-3 py-2 rounded-pill">OUR PORTFOLIO</span>
            <h1 class="fw-bold display-5">Recent Memories & Events</h1>
            <p class="text-muted lead">Take a look at some of the magical moments we've crafted for our clients.</p>
        </div>
    </section>

    <section class="py-5" id="gallery">
        <div class="container py-4">
            <div class="row g-4">
                @forelse($gallery ?? [] as $photo)
                    <div class="col-lg-3 col-md-6">
                        <div class="card shadow-sm border-0 h-100 overflow-hidden">
                            <div class="position-relative" style="height: 250px;">
                                <img src="{{ asset('storage/' . $photo->image_path) }}" class="w-100 h-100 object-fit-cover" alt="{{ $photo->title }}">
                                @if($photo->is_featured)
                                    <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark">Featured</span>
                                @endif
                            </div>
                            <div class="card-body bg-white">
                                <h5 class="card-title fs-6 fw-bold text-dark mb-1">{{ $photo->title }}</h5>
                                @if($photo->description)
                                    <p class="card-text text-muted small mb-0">{{ Str::limit($photo->description, 60) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-secondary d-inline-block px-5 py-3">
                            <p class="mb-0">No gallery images available at the moment. Please check back later!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(method_exists($gallery ?? null, 'links'))
                <div class="mt-5 d-flex justify-content-center">
                    {{ $gallery->links() }}
                </div>
            @endif
        </div>
    </section>
</main>
@endsection
