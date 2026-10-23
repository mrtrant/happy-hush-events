<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Happy Hush Events Kenya')
    </title>

    <meta name="description"
          content="@yield('description', 'Happy Hush Events Kenya - Event planners, decorations, photography, entertainment, catering and unforgettable celebrations.')">

    <meta name="keywords"
          content="Happy Hush, Events Kenya, Birthday, Wedding, Graduation, Decorations, Photography">

    <meta name="author"
          content="Happy Hush Events Kenya">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title"
          content="Happy Hush Events Kenya">

    <meta property="og:description"
          content="Happy Hush Events Kenya - Event planners, decorations, photography, entertainment, catering and unforgettable celebrations.">

    <meta property="og:image"
          content="{{ asset('assets/images/logo/logo.png') }}">

    <meta name="theme-color"
          content="#BF3604">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    @stack('styles')

</head>

<body>

<div class="website-wrapper">

    @include('components.website.navbar')

    <main>

        @yield('content')

    </main>

    @include('components.website.footer')

</div>

@stack('scripts')

<a href="https://wa.me/254700000000"

class="whatsapp-float"

target="_blank">

💬

</a>
<script>

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.counter').forEach(counter => {

        const target = parseInt(counter.dataset.target);

        let value = 0;

        const step = Math.max(1, Math.ceil(target / 80));

        function animate() {

            value += step;

            if (value >= target) {

                counter.innerText = target.toLocaleString();

                return;

            }

            counter.innerText = value.toLocaleString();

            requestAnimationFrame(animate);

        }

        animate();

    });

});

</script>

</body>

</html>
