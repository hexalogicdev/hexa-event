@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Register')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
    <link rel="stylesheet" href="{{ asset('assets/css/custom-style.css') }}">

@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">

                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="{{ url('/') }}" class="app-brand-link">
                                <span class="">@include('_partials.macros')</span>
                                {{-- <span class="app-brand-text demo text-heading fw-bold">DomeCarlow</span> --}}
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1">Sign Up </h4>
                        {{-- 🚀 --}}
                        <p class="mb-6">Sign up to enroll your child in online Quran classes.</p>

                        <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="mb-6">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="username"
                                    placeholder="Enter your name" autocomplete="name" required minlength="3" maxlength="50"
                                    title="Name should be between 3 and 50 characters">
                            </div>

                            <div class="mb-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" autocomplete="email" required maxlength="100"
                                    title="Enter a valid email address">
                            </div>

                            <div class="mb-6">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    placeholder="Enter your phone (e.g., +1234567890)" autocomplete="tel" required
                                    pattern="^\+?[0-9]{10,15}$" title="Phone must be 10 to 15 digits and can start with +">
                            </div>

                            <div class="mb-6">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address"
                                    placeholder="Enter your address" autocomplete="street-address" required minlength="5"
                                    maxlength="100" title="Address should be between 5 and 100 characters">
                            </div>

                            <div class="mb-6">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <select name="country" class="form-control" id="country" required>
                                    <option value="" disabled selected>Select your country</option>
                                    <option value="USA">USA</option>
                                    <option value="UK">UK</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Ireland">Ireland</option>
                                    <option value="Pakistan">Pakistan</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-control" id="gender" required>
                                    <option value="" disabled selected>Select your gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>

                            <div class="mb-6 form-password-toggle">
                                <label class="form-label" for="password">Password <i class="tex-danger">*</i></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"required
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="mb-6 form-password-toggle">
                                <label class="form-label" for="password-confirm">Confirm Password <i
                                        class="tex-danger">*</i></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password-confirm" class="form-control"required
                                        name="password_confirmation"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="my-8">
                                <div class="form-check mb-0 ms-2">
                                    <input class="form-check-input p-0" type="checkbox" id="terms-conditions"
                                        name="terms">
                                    <label class="form-check-label" for="terms-conditions">
                                        I agree to
                                        <a href="javascript:void(0);">privacy policy & terms</a>
                                    </label>
                                </div>
                            </div>
                            <button type="submit"
                                class="btn_continue cta text-white cta-100 main-continue btn btn-primary d-grid w-100">
                                Sign up
                            </button>
                        </form>

                        <p class="text-center">
                            <span>Already have an account?</span>
                            <a href="{{ url('login') }}">
                                <span>Sign in instead</span>
                            </a>
                        </p>

                        {{-- <div class="divider my-6">
            <div class="divider-text">or</div>
          </div>

          <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-facebook me-1_5">
              <i class="tf-icons ti ti-brand-facebook-filled"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-twitter me-1_5">
              <i class="tf-icons ti ti-brand-twitter-filled"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-github me-1_5">
              <i class="tf-icons ti ti-brand-github-filled"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-google-plus">
              <i class="tf-icons ti ti-brand-google-filled"></i>
            </a>
          </div> --}}
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
@endsection
