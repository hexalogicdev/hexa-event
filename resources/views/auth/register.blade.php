@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.app')

@section('title', 'Register')

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
                    <div class="fw-semibold mb-1">Please review the highlighted details.</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="row g-0 ems-fullscreen">
            <div class="col-lg-5 d-none d-lg-block ems-hero">
                <div class="p-5 h-100 d-flex flex-column justify-content-between">

                    <div class="mt-5">
                        <span class="ems-kicker mb-4 bg-white bg-opacity-10 text-white">
                            <i class="ti ti-rocket"></i>
                            Build with HexaLogicDev
                        </span>
                        <h1 class="display-4 fw-bold mb-3">Start your event workspace in minutes.</h1>
                        <p class="lead mb-4">
                            Create an organizer account to publish events, manage guests, and run check-in flows from a
                            polished international system.
                        </p>
                        <ul class="ems-support-list">
                            <li>
                                <i class="ti ti-user-shield"></i>
                                <div>
                                    <div class="fw-semibold">Organizer role</div>
                                    <div class="text-white-50">Create and manage only your own events.</div>
                                </div>
                            </li>
                            <li>
                                <i class="ti ti-ticket"></i>
                                <div>
                                    <div class="fw-semibold">Public event pages</div>
                                    <div class="text-white-50">Share booking links with unique QR confirmation.</div>
                                </div>
                            </li>
                            <li>
                                <i class="ti ti-world"></i>
                                <div>
                                    <div class="fw-semibold">International format</div>
                                    <div class="text-white-50">Responsive layout and global-ready design.</div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                <div class="small text-white-50">Global</div>
                                <div class="h4 mb-0 fw-bold">Ready</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                <div class="small text-white-50">Design</div>
                                <div class="h4 mb-0 fw-bold">Premium</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                <div class="small text-white-50">Flow</div>
                                <div class="h4 mb-0 fw-bold">Fast</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 ems-auth-surface">
                <div class="d-flex flex-column justify-content-center h-100 p-4 p-lg-5 ems-auth-panel">
                    <div class="auth-mobile-brand d-lg-none mb-4 text-center">
                        <img src="{{ asset('assets/img/hexa-logo.png') }}" alt="HexaLogicDev" class="ems-hero-logo mx-auto">
                    </div>

                    <div class="mb-4 text-center text-lg-start">
                        <span class="ems-badge ems-badge--info mb-3">Create account</span>
                        <h2 class="ems-section-title display-6 mb-2">Join the event management platform</h2>
                        <p class="ems-section-subtitle mb-0">
                            Fill in your details to create and manage events with a clean, global-ready interface.
                        </p>
                    </div>

                    <form id="formAuthentication" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="ems-form-grid">
                            <div class="ems-form-span-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="username"
                                    placeholder="Your full name" value="{{ old('username') }}" autocomplete="name"
                                    required minlength="3" maxlength="50">
                            </div>

                            <div class="ems-form-span-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email"
                                    placeholder="name@company.com" value="{{ old('email') }}" autocomplete="email"
                                    required maxlength="100">
                            </div>

                            <div class="ems-form-span-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone"
                                    placeholder="+1234567890" value="{{ old('phone') }}" autocomplete="tel" required
                                    pattern="^\+?[0-9]{10,15}$">
                            </div>

                            <div class="ems-form-span-6">
                                <label for="country" class="form-label">Country</label>
                                <select name="country" class="form-select form-select-lg" id="country" required>
                                    <option value="" disabled @selected(!old('country'))>Select country</option>
                                    <option value="USA" @selected(old('country') === 'USA')>USA</option>
                                    <option value="UK" @selected(old('country') === 'UK')>UK</option>
                                    <option value="Canada" @selected(old('country') === 'Canada')>Canada</option>
                                    <option value="Australia" @selected(old('country') === 'Australia')>Australia</option>
                                    <option value="Ireland" @selected(old('country') === 'Ireland')>Ireland</option>
                                    <option value="Pakistan" @selected(old('country') === 'Pakistan')>Pakistan</option>
                                </select>
                            </div>

                            <div class="ems-form-span-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control form-control-lg" id="address" name="address"
                                    placeholder="Street, city, region" value="{{ old('address') }}" autocomplete="street-address"
                                    required minlength="5" maxlength="100">
                            </div>

                            <div class="ems-form-span-6">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" class="form-select form-select-lg" id="gender" required>
                                    <option value="" disabled @selected(!old('gender'))>Select gender</option>
                                    <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                                    <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                                </select>
                            </div>

                            <div class="ems-form-span-6">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control form-control-lg"
                                        name="password" placeholder="Create a secure password" autocomplete="new-password"
                                        required>
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>

                            <div class="ems-form-span-12">
                                <label for="password-confirm" class="form-label">Confirm password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password-confirm" class="form-control form-control-lg"
                                        name="password_confirmation" placeholder="Repeat your password"
                                        autocomplete="new-password" required>
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mt-4 mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms">
                                <label class="form-check-label" for="terms-conditions">
                                    I agree to the <a href="{{ route('legal.policy') }}" target="_blank" rel="noopener noreferrer">privacy policy and terms</a>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Create account
                        </button>
                    </form>

                    <div class="mt-4 text-center text-lg-start">
                        <span class="text-muted">Already have an account?</span>
                        <a href="{{ url('login') }}" class="fw-semibold text-decoration-none ms-1">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
