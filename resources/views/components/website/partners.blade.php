<section class="py-5 bg-white">

    <div class="container">

        <div class="text-center mb-5">

            <span class="section-badge">

                TRUSTED BY

            </span>

            <h2 class="section-title">

                Organizations & Clients We Proudly Serve

            </h2>

            <p class="text-muted">

                Building lasting relationships through exceptional event experiences.

            </p>

        </div>

        <div class="row g-4 text-center">

            @for($i = 1; $i <= 6; $i++)

                <div class="col-lg-2 col-md-4 col-6">

                    <div class="partner-card">

                        <img
                            src="{{ asset('assets/images/logo/logo-icon.png') }}"
                            class="partner-logo"
                            alt="Partner Logo">

                    </div>

                </div>

            @endfor

        </div>

    </div>

</section>
