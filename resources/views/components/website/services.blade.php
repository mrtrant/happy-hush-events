@php
use Illuminate\Support\Str;
@endphp
<section class="py-5" id="services">

    <div class="container">

        <div class="row mb-5">

            <div class="col-lg-6">

                <span class="section-badge">
                    OUR SERVICES
                </span>

                <h2 class="section-title">
                    Our Professional Services
                </h2>

            </div>

            <div class="col-lg-6 d-flex align-items-center">

                <p class="text-muted">

                    Browse some of the services offered by Happy Hush Events Kenya.

                </p>

            </div>

        </div>

        <div class="row g-4">

            @forelse($services as $service)

                <div class="col-lg-4 col-md-6">

                    <div class="service-card h-100">

                        @if($service->image)

                            <img
                                src="{{ asset($service->image) }}"
                                class="img-fluid rounded mb-3"
                                alt="{{ $service->name }}">

                        @endif

                        <h4>

                            {{ $service->name }}

                        </h4>

                        <p>

                            {{ Str::limit($service->description,120) }}

                        </p>

                        <div class="mt-3">

                            <strong>

                                KES {{ number_format($service->base_price,2) }}

                            </strong>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="alert alert-warning">

                        No services have been added yet.

                    </div>

                </div>

            @endforelse

        </div>

    </div>

</section>
