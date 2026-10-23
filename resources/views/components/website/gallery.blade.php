<section class="py-5" id="gallery">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge">
                OUR GALLERY
            </span>
            <h2 class="section-title">
                Recent Memories
            </h2>
        </div>
        <div class="row g-4">
            @forelse($gallery as $photo)
                <div class="col-lg-3 col-md-6">
                    <div class="gallery-item">
                        <img
                            src="{{ asset('storage/' . $photo->image_path) }}"
                            class="img-fluid"
                            alt="{{ $photo->title }}">
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary">
                        Gallery images will appear here once uploaded.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
