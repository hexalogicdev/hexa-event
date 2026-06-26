@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.app')

@section('title', 'Login')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
    <div class="container-fluid px-0 ems-auth-shell">
        @if ($errors->any())
            <div class="container pt-3">
                <div class="alert alert-danger border-0 rounded-4 mb-0">
                    <div class="fw-semibold mb-1">We could not sign you in.</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="row g-0 ems-fullscreen">
            <div class="col-lg-6 d-none d-lg-block ems-hero">
                <div class="p-5 h-100 d-flex flex-column justify-content-between">

                    <div class="mt-5">
                        <span class="ems-kicker mb-4 bg-white bg-opacity-10 text-white">
                            <i class="ti ti-shield-lock"></i>
                            Secure event operations
                        </span>
                        <h1 class="display-4 fw-bold mb-3">A premium command center for event teams.</h1>
                        <p class="lead mb-4">
                            Manage registrations, QR check-ins, and reporting from a polished international dashboard.
                        </p>
                        <ul class="ems-support-list">
                            <li>
                                <i class="ti ti-layout-dashboard"></i>
                                <div>
                                    <div class="fw-semibold">Organizer dashboard</div>
                                    <div class="text-white-50">Clean metrics and live status in one view.</div>
                                </div>
                            </li>
                            <li>
                                <i class="ti ti-qrcode"></i>
                                <div>
                                    <div class="fw-semibold">QR check-in</div>
                                    <div class="text-white-50">Fast venue verification with password protection.</div>
                                </div>
                            </li>
                            <li>
                                <i class="ti ti-language"></i>
                                <div>
                                    <div class="fw-semibold">International by design</div>
                                    <div class="text-white-50">Responsive, locale-friendly structure.</div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                <div class="small text-white-50">Projects</div>
                                <div class="h4 mb-0 fw-bold">150+</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                <div class="small text-white-50">Clients</div>
                                <div class="h4 mb-0 fw-bold">80+</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                <div class="small text-white-50">Retention</div>
                                <div class="h4 mb-0 fw-bold">98%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 ems-auth-surface">
                <div class="d-flex flex-column justify-content-center h-100 p-4 p-lg-5 ems-auth-panel">
                    <div class="auth-mobile-brand d-lg-none mb-4 text-center">
                        <img src="{{ asset('assets/img/hexa-logo.png') }}" alt="HexaLogicDev" class="ems-hero-logo mx-auto">
                    </div>

                    <div class="mb-4 text-center text-lg-start">
                        <span class="ems-badge ems-badge--info mb-3">Organizer access</span>
                        <h2 class="ems-section-title display-6 mb-2">Sign in to your workspace</h2>
                        <p class="ems-section-subtitle mb-0">
                            Use your account to manage events, guests, and attendance reports.
                        </p>
                    </div>

                    <form id="formAuthentication" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email"
                                placeholder="name@company.com" value="{{ old('email') }}" autocomplete="email" autofocus
                                required>
                        </div>

                        <div class="mb-4 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control form-control-lg" name="password"
                                    placeholder="Enter your password" autocomplete="current-password" required>
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                <label class="form-check-label" for="remember-me">Remember me</label>
                            </div>
                            <a href="{{ url('password/reset') }}" class="text-decoration-none">Forgot password?</a>
                        </div>

                        <button class="btn btn-primary btn-lg w-100" type="submit">
                            Sign in
                        </button>
                    </form>

                    <div class="mt-4 text-center text-lg-start">
                        <span class="text-muted">New here?</span>
                        <a href="{{ url('register') }}" class="fw-semibold text-decoration-none ms-1">Create an account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
