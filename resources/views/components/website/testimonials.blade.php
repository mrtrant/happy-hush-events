<section id="testimonials" class="py-5 bg-light">

    <div class="container">

        <div class="text-center mb-5">

            <span class="section-badge">

                TESTIMONIALS

            </span>

            <h2 class="section-title">

                Happy Clients

            </h2>

        </div>

        <div class="row g-4">

            @forelse($testimonials as $testimonial)

                <div class="col-lg-4">

                    <div class="testimonial-card h-100">

                        @if(!empty($testimonial->image_path))

                            <img
                                src="{{ asset($testimonial->image_path) }}"
                                class="rounded-circle mb-3"
                                width="80"
                                height="80"
                                alt="{{ $testimonial->name }}">

                        @endif

                        <h5>

                            {{ $testimonial->name }}

                        </h5>

                        <p>

                            {{ $testimonial->message }}

                        </p>

                        <div class="text-warning fs-5">

                            @for($i = 1; $i <= 5; $i++)

                                @if($i <= $testimonial->rating)

                                    ★

                                @else

                                    ☆

                                @endif

                            @endfor

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="alert alert-warning">

                        No testimonials are available yet.

                    </div>

                </div>

            @endforelse

        </div>

    </div>

</section>
