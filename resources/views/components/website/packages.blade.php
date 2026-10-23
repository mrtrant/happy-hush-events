<section class="py-5 bg-light" id="packages">

    <div class="container">

        <div class="text-center mb-5">

            <span class="section-badge">

                POPULAR PACKAGES

            </span>

            <h2 class="section-title">

                Choose Your Package

            </h2>

        </div>

        <div class="row g-4">

            @forelse($packages as $package)

                <div class="col-lg-4">

                    <div class="package-card h-100">

                        @if(!empty($package->image))

                            <img
                                src="{{ asset($package->image) }}"
                                class="img-fluid rounded mb-3"
                                alt="{{ $package->name }}">

                        @endif

                        <h3>

                            {{ $package->name }}

                        </h3>

                        <p>

                            {{ \Illuminate\Support\Str::limit($package->description, 120) }}

                        </p>

                        <h2 class="mt-4">

                            KES {{ number_format((float) $package->package_price, 2) }}

                        </h2>

                        <a href="#"
                           class="btn btn-brand mt-4">

                            View Package

                        </a>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="alert alert-info">

                        Packages will appear here once added.

                    </div>

                </div>

            @endforelse

        </div>

    </div>

</section>
