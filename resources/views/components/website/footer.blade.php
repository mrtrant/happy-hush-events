<footer class="footer bg-dark text-white pt-5 pb-4">
    <div class="container">
        <div class="row gy-5">

            <!-- Column 1: About / Logo -->
            <div class="col-lg-3">
                <img
                    src="{{ asset('assets/images/logo/logo-white.png') }}"
                    class="footer-logo mb-4 img-fluid"
                    style="max-width: 160px;"
                    alt="Happy Hush Events">
                <p class="text-white-50 small">
                    Happy Hush Events Kenya delivers exceptional event planning,
                    entertainment, décor, photography, catering and memorable
                    experiences across Kenya.
                </p>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="col-lg-2">
                <h5 class="text-white fw-bold mb-3">Company</h5>
                <ul class="footer-links list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-white-50 text-decoration-none">About</a></li>
                    <li class="mb-2"><a href="{{ route('services') }}" class="text-white-50 text-decoration-none">Services</a></li>
                    <li class="mb-2"><a href="{{ route('packages') }}" class="text-white-50 text-decoration-none">Packages</a></li>
                    <li class="mb-2"><a href="{{ route('gallery') }}" class="text-white-50 text-decoration-none">Gallery</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}" class="text-white-50 text-decoration-none">Contact</a></li>
                </ul>
            </div>

            <!-- Column 3: Contact Info -->
            <div class="col-lg-3">
                <h5 class="text-white fw-bold mb-3">Contact</h5>
                <p class="text-white-50 mb-2"><strong>Location:</strong> Nairobi, Kenya</p>
                <p class="text-white-50 mb-2"><strong>Phone:</strong> +254 797541131</p>
                <p class="text-white-50 mb-2"><strong>Email:</strong> happyhush.ke@gmail.com</p>
            </div>

            <!-- Column 4: Social Media Links -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <h5 class="text-white fw-bold mb-3">Follow Us</h5>
                <p class="text-white-50 small mb-3">Connect with us on our social media platforms for updates and event inspiration.</p>

                <!-- Use flex-column so items stack vertically on phones and don't overlap -->
                <div class="d-flex flex-column gap-2">
                    <a href="https://facebook.com/yourpage" target="_blank" class="text-white-50 text-decoration-none d-flex align-items-center gap-2">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                    <a href="https://instagram.com/original.hhe" target="_blank" class="text-white-50 text-decoration-none d-flex align-items-center gap-2">
                        <i class="bi bi-instagram"></i> @original.hhe (Instagram)
                    </a>
                    <a href="https://wa.me/254797541131" target="_blank" class="text-white-50 text-decoration-none d-flex align-items-center gap-2">
                        <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>

        </div>

        <hr class="my-4 border-secondary">

        <!-- Bottom Copyright Row -->
        <div class="row text-white-50 small">
            <div class="col-md-6">
                &copy; {{ date('Y') }} Happy Hush Events Kenya. All rights reserved.
            </div>
            <div class="col-md-6 text-md-end">
                Designed & Powered by Happy Hush & Mkenya Mjanja
            </div>
        </div>
    </div>
</footer>
