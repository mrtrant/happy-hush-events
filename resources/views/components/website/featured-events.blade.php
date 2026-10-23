<section class="py-5 bg-light">

    <div class="container">

        <div class="row mb-5">

            <div class="col text-center">

                <span class="section-badge">
                    OUR EVENTS
                </span>

                <h2 class="section-title mt-3">

                    Featured Events

                </h2>

                <p class="section-subtitle">

                    Some of the memorable events we've successfully organized.

                </p>

            </div>

        </div>

        <div class="row">

            @forelse($featuredEvents as $event)

                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body">

                            <span class="badge bg-success mb-3">

                                {{ $event->status }}

                            </span>

                            <h4>

                                {{ $event->event_name }}

                            </h4>

                            <hr>

                            <p>

                                <strong>Event Type</strong><br>

                                {{ optional($event->eventType)->name }}

                            </p>

                            <p>

                                <strong>Venue</strong><br>

                                {{ optional($event->venue)->name ?? 'To Be Confirmed' }}

                            </p>

                            <p>

                                <strong>Date</strong><br>

                                {{ optional($event->event_date)->format('d M Y') }}

                            </p>

                            <p>

                                <strong>Guests</strong><br>

                                {{ number_format($event->guest_count) }}

                            </p>

                            <p>

                                <strong>Booking</strong><br>

                                {{ optional($event->booking)->booking_number }}

                            </p>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="alert alert-info text-center">

                        Featured events will appear here.

                    </div>

                </div>

            @endforelse

        </div>

    </div>

</section>
