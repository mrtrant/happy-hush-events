<section class="py-5 bg-light">

    <div class="container">

        <div class="row mb-5">

            <div class="col-lg-6">

                <span class="section-badge">

                    RECENT EVENTS

                </span>

                <h2 class="section-title">

                    Events We've Recently Delivered

                </h2>

            </div>

            <div class="col-lg-6 d-flex align-items-center">

                <p class="text-muted mb-0">

                    From weddings to corporate functions, every event is planned with precision and creativity.

                </p>

            </div>

        </div>

        <div class="row g-4">

            @for($i = 1; $i <= 3; $i++)

                <div class="col-lg-4">

                    <div class="event-showcase">

                        <img
                            src="{{ asset('assets/images/gallery/placeholder.jpg') }}"
                            class="img-fluid"
                            alt="Event">

                        <div class="p-4">

                            <span class="badge bg-warning text-dark">

                                Wedding

                            </span>

                            <h4 class="mt-3">

                                Elegant Garden Wedding

                            </h4>

                            <p>

                                Beautiful outdoor wedding featuring premium décor,
                                photography and full event coordination.

                            </p>

                        </div>

                    </div>

                </div>

            @endfor

        </div>

    </div>

</section>
