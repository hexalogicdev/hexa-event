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
    <meta name="theme-color" content="#4f46e5">
    <title>@yield('title', $appName) | {{ $appName }}</title>
    @vite(['resources/assets/vendor/fonts/tabler-icons.scss', 'resources/css/app.css', 'resources/css/custom-style.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/public-site.css') }}?v={{ filemtime(public_path('assets/css/public-site.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/event-platform.css') }}?v={{ filemtime(public_path('assets/css/event-platform.css')) }}">
    @yield('vendor-style')
    @yield('page-style')
    @stack('styles')
</head>

<body class="ems-guest-body public-site {{ $isAuthPage ? 'ems-auth-body' : '' }}">
    <div class="ems-guest-shell">
        @include('layouts.partials.public-header')

        <main class="ems-page">
            @yield('content')
        </main>

        @unless ($isAuthPage)
            <footer class="eventify-footer" id="eventify-footer"><div class="container"><div class="row g-4 eventify-footer-grid">
                        <div class="col-lg-4"><a class="eventify-footer-brand" href="{{ url('/') }}"><span class="eventify-brand-mark"><i class="ti ti-calendar-event"></i></span>{{ $appName }}</a><p>Making event discovery and registration simple, secure, and enjoyable.</p></div>
                        <div class="col-6 col-lg-2"><h2>Quick links</h2><a href="{{ url('/') }}">Home</a><a href="{{ url('/') }}#events">Events</a><a href="{{ url('/') }}#categories">Categories</a><a href="{{ url('/') }}#why-us">About us</a></div>
                        <div class="col-6 col-lg-2"><h2>Support</h2><a href="{{ route('legal.policy') }}">Privacy policy</a><a href="{{ route('legal.policy') }}">Terms</a><a href="{{ route('legal.policy') }}">Refund policy</a><a href="mailto:support@example.com">Contact</a></div>
                        <div class="col-lg-4"><h2>Stay informed</h2><p>Discover newly published events and booking updates.</p><a class="btn btn-eventify" href="{{ url('/') }}#events">Explore events <i class="ti ti-arrow-right"></i></a></div>
                    </div>
                    <div class="eventify-footer-bottom"><span>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</span><span>Built for memorable experiences.</span></div></div>
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
