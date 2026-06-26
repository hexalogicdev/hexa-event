<!doctype html>
@php
    $locale = str_replace('_', '-', app()->getLocale());
    $direction = in_array(app()->getLocale(), ['ar', 'fa', 'he', 'ur']) ? 'rtl' : 'ltr';
    $appName = config('app.name', 'Event Management System');
    $isAuthPage = request()->routeIs('login', 'register', 'password.*');
    $isCheckinPage = request()->routeIs('attendee.qr*');
    $flashSuccess = session('success');
    $flashError = session('error');
    $validationErrors = $errors->all();
@endphp
<html lang="{{ $locale }}" dir="{{ $direction }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f766e">
    <title>@yield('title', $appName) | {{ $appName }}</title>
    @vite(['resources/assets/vendor/fonts/tabler-icons.scss', 'resources/css/app.css', 'resources/css/custom-style.css', 'resources/js/app.js'])
    @yield('vendor-style')
    @yield('page-style')
    @stack('styles')
</head>

<body class="ems-guest-body {{ $isAuthPage ? 'ems-auth-body' : '' }}">
    <div class="ems-guest-shell">
        <header class="ems-topbar">
            <nav class="navbar navbar-expand-lg navbar-dark ems-nav">
                <div class="container-fluid px-3 px-lg-4">
                    <a class="navbar-brand d-flex align-items-center gap-2 gap-lg-3 ems-brand m-0" href="{{ url('/') }}">
                        <img src="{{ asset('assets/img/hexa-logo.png') }}" alt="HexaLogicDev" class="ems-brand-logo">
                    </a>
                    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#guestNavbar" aria-controls="guestNavbar" aria-expanded="false"
                        aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse mt-3 mt-lg-0" id="guestNavbar">
                        <div class="ms-lg-auto ems-topbar-actions d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 gap-lg-3 w-100 w-lg-auto">
                            <span class="ems-pill justify-content-center justify-content-lg-start d-none d-md-inline-flex">
                                <i class="ti ti-world"></i>
                                Global-ready
                            </span>
                            <a class="btn btn-outline-light px-4 {{ $isCheckinPage ? 'active' : '' }}" href="{{ route('attendee.qr.scan') }}">
                                <i class="ti ti-qrcode me-2"></i>
                                Scan QR
                            </a>
                            <a class="btn btn-outline-light px-4" href="{{ route('legal.policy') }}">
                                <i class="ti ti-file-text me-2"></i>
                                Policy
                            </a>
                            @auth
                                <a class="btn btn-primary px-4" href="{{ route('dashboard') }}">Dashboard</a>
                            @else
                                <a class="btn btn-outline-primary px-4" href="{{ route('login') }}">Log in</a>
                                <a class="btn btn-primary px-4" href="{{ route('register') }}">Sign up</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <main class="ems-page">
            @yield('content')
        </main>

        @unless ($isAuthPage)
            <footer class="py-4 mt-auto">
                <div class="container">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                        <p class="mb-0 text-muted">
                            &copy; {{ date('Y') }} {{ $appName }}. Built for international event teams.
                        </p>
                        <p class="mb-0 text-muted">
                            Public bookings, secure QR verification, multilingual-ready layouts.
                        </p>
                    </div>
                </div>
            </footer>
        @endunless
    </div>

    <script>
        window.successMessage = @json($flashSuccess);
        window.errorMessage = @json($flashError);
        window.validationErrors = @json($validationErrors);
    </script>

    @yield('vendor-script')
    @yield('page-script')
    @stack('scripts')
</body>

</html>
