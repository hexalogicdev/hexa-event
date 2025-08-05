@extends('layouts.main')

@section('title', 'Contact Us - Online Quran Learn')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <h1 class="mb-4 text-primary text-center">Get In Touch</h1>
        <p class="text-center mb-5">
            Have questions or want to enroll? Fill out the form below and we will get back to you shortly.
        </p>

        <div class="row justify-content-center">
            <div class="col-md-7">
                <form action="#" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Your full name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="your.email@example.com" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="+1234567890" value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label fw-semibold">Message</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="Your message here..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
