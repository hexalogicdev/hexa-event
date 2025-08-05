@extends('layouts.main')

@section('title', 'About Us - Online Quran Learn')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <h1 class="mb-4 text-primary">About Online Quran Learn</h1>
        <p class="lead mb-4">
            At Online Quran Learn, we are dedicated to providing high-quality Quran education accessible to everyone, anywhere. Our mission is to help students learn Quran with Tajweed, memorization, and understanding through expert tutors and personalized lessons.
        </p>

        <div class="row align-items-center gy-4">
            <div class="col-md-6">
                <img src="{{ asset('assets/img/about-quran-study.jpg') }}" alt="About Quran Study" class="img-fluid rounded shadow" />
            </div>
            <div class="col-md-6">
                <h3>Our Approach</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Qualified and experienced Quran tutors</li>
                    <li class="list-group-item">One-on-one live online classes</li>
                    <li class="list-group-item">Flexible schedule tailored to your needs</li>
                    <li class="list-group-item">Focus on Tajweed, memorization, and Quranic understanding</li>
                    <li class="list-group-item">Ongoing progress tracking and support</li>
                </ul>
            </div>
        </div>

        <div class="mt-5 text-center">
            <a href="{{ url('/packages') }}" class="btn btn-primary btn-lg">Explore Our Packages</a>
        </div>
    </div>
</section>
@endsection
