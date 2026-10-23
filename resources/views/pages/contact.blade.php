@extends('layouts.website')

@section('title', 'Contact Us - Happy Hush Events Kenya')

@section('content')
<main>
    <section class="py-5 bg-light text-center">
        <div class="container py-4">
            <span class="badge bg-primary text-white mb-2 px-3 py-2 rounded-pill">GET IN TOUCH</span>
            <h1 class="fw-bold display-5">Contact Us</h1>
            <p class="text-muted lead">Ready to plan your next event? Reach out to our team today.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="row g-5">
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-4">Send Us a Message</h3>
                    @if(session('success'))
                        <div class="alert alert-success mb-4">{{ session('success') }}</div>
                    @endif
                    <form action="#" method="POST" class="space-y-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Your Full Name *</label>
                            <input type="text" name="name" required class="form-control" placeholder="Samuel Muturi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Address *</label>
                            <input type="email" name="email" required class="form-control" placeholder="samuel@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+254 700 000000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Message / Inquiry Details *</label>
                            <textarea name="message" rows="4" required class="form-control" placeholder="Tell us about your event date, location, and needs..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 py-2">Submit Inquiry</button>
                    </form>
                </div>
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-4">Contact Information</h3>
                    <p class="text-muted mb-4">Feel free to contact me directly via phone or email.</p>
                    <ul class="list-unstyled space-y-3">
                        <li class="mb-3"><strong>Location:</strong> Nairobi, Kenya</li>
                        <li class="mb-3"><strong>Phone:</strong> +254 797541131</li>
                        <li class="mb-3"><strong>Email:</strong> happyhusheventskenya@gmail.com</li>
                        <li class="mb-3"><strong>Working Hours:</strong> Mon - Sut: 8:00 AM - 6:00 PM</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
