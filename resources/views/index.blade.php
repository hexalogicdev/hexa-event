@extends('layouts.main')

@section('title', 'Home - Online Quran Learn')

@section('content')
<!-- Hero Section -->
<section class="bg-primary text-white text-center py-5">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Learn Quran Online with Expert Tutors</h1>
        <p class="lead mb-4">Flexible timings | Personalized lessons | Tajweed & Memorization</p>
        <a href="{{ url('/packages') }}" class="btn btn-light btn-lg px-4">Explore Packages</a>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center gy-4">
            <div class="col-md-4">
                <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                <h4 class="mb-2">Qualified Tutors</h4>
                <p>Learn from experienced Quran teachers dedicated to your progress.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                <h4 class="mb-2">Flexible Schedule</h4>
                <p>Choose class timings that fit your lifestyle, anytime, anywhere.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                <h4 class="mb-2">Certification</h4>
                <p>Get certified after course completion to showcase your achievement.</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-md-6">
                <img src="{{ asset('assets/img/quran-study.jpg') }}" alt="Quran Study" class="img-fluid rounded shadow" />
            </div>
            <div class="col-md-6">
                <h2 class="mb-3">Why Choose Online Quran Learn?</h2>
                <p>Our platform offers personalized Quran learning with live online sessions, interactive tools, and supportive community. Whether you are a beginner or want to improve your Tajweed and memorization, we have tailored programs for every learner.</p>
                <a href="{{ url('/about') }}" class="btn btn-primary mt-3">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 text-center">
    <div class="container">
        <h3 class="mb-4">Ready to Start Your Quran Learning Journey?</h3>
        <a href="{{ url('/contact') }}" class="btn btn-lg btn-primary px-5">Contact Us Today</a>
    </div>
</section>
@endsection

