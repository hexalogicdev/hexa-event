<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Event Management System</title>
    <link rel="icon" href="{{ asset('assets/img/black_transparent.png') }}" sizes="32x32">
    <link rel="icon" href="assets/img/emerald-park-web-logo-192.png" sizes="192x192">
    <link rel="icon" href="assets/img/emerald-park-web-logo-180.png" sizes="180x180">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-style.css') }}">
    <link rel="stylesheet" href="cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body>
    <style>
        .navbar-nav .nav-link {
            transition: color 0.3s ease;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .navbar-nav .nav-link:hover {
            color: #0d6efd !important;
            /* Bootstrap primary blue */
            background-color: #e7f1ff;
        }

        .navbar-nav .active {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 0.3rem;
        }
    </style>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('assets/img/black_transparent.png') }}" alt="Online Quran Learn" height="50"
                        class="me-2" />
                    <span class="fs-4 fw-semibold text-primary">Online Quran Learn</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav gap-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? ' fw-bold text-primary' : 'text-secondary' }}"
                                href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('about') ? ' fw-bold text-primary' : 'text-secondary' }}"
                                href="{{ url('/about') }}">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('packages') ? ' fw-bold text-primary' : 'text-secondary' }}"
                                href="{{ url('/packages') }}">Packages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('contact') ? ' fw-bold text-primary' : 'text-secondary' }}"
                                href="{{ url('/contact') }}">Contact</a>
                        </li>
                        {{-- Add more tabs as needed --}}
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    @yield('content')
    <footer class="bg-light text-center text-muted py-3 mt-auto">
        <div class="container">
            &copy; {{ date('Y') }} Online Quran Learn. All rights reserved.
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('script')
</body>

</html>
