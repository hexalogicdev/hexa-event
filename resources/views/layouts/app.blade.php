<!doctype html>
@php
    $locale = str_replace('_', '-', app()->getLocale());
    $direction = in_array(app()->getLocale(), ['ar', 'fa', 'he', 'ur']) ? 'rtl' : 'ltr';
    $appName = config('app.name', 'Event Management System');
    $isAuthPage = request()->routeIs('login', 'password.*');
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
    <link rel="stylesheet" href="{{ asset('assets/css/public-site.css') }}?v={{ filemtime(public_path('assets/css/public-site.css')) }}">
    @yield('vendor-style')
    @yield('page-style')
    @stack('styles')
</head>

<body class="ems-guest-body public-site {{ $isAuthPage ? 'ems-auth-body' : '' }}">
    <div class="ems-guest-shell">
        <header class="ems-topbar">
            <nav class="navbar navbar-expand-lg navbar-light ems-nav">
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
                            <a class="ems-public-nav-link" href="{{ url('/') }}#events">Explore events</a>
                            {{-- <a class="ems-public-nav-link" href="{{ route('legal.policy') }}">Privacy</a> --}}
                            @auth
                                <a class="btn btn-sm btn-primary px-4" href="{{ route('dashboard') }}">Dashboard</a>
                            @else
                                <a class="btn btn-sm btn-dark px-4" href="{{ route('login') }}">Team login</a>
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
                            &copy; {{ date('Y') }} {{ $appName }}. Discover and book memorable events.
                        </p>
                        <p class="mb-0 text-muted">
                            Event information, ticket selection, and simple online registration.
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
