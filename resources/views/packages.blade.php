@extends('layouts.main')

@section('title', 'Packages - Online Quran Learn')

@section('content')
<section class="py-5">
    <div class="container">
        <h1 class="mb-4 text-primary text-center">Our Quran Learning Packages</h1>
        <p class="text-center mb-5">
            Choose a package that fits your goals and schedule. Whether you want to learn basic Quran reading or master Tajweed and memorization, we have options for every learner.
        </p>

        <div class="row gy-4 justify-content-center">
            <!-- Example Package Card -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white text-center fs-5 fw-semibold">
                        Beginner Package
                    </div>
                    <div class="card-body">
                        <ul class="mb-3">
                            <li>30 minutes per session</li>
                            <li>2 sessions per week</li>
                            <li>Basic Quran reading</li>
                            <li>Introduction to Tajweed</li>
                        </ul>
                        <h4 class="text-center text-success">$50 / month</h4>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ url('/contact') }}" class="btn btn-primary">Enroll Now</a>
                    </div>
                </div>
            </div>

            <!-- Package 2 -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white text-center fs-5 fw-semibold">
                        Intermediate Package
                    </div>
                    <div class="card-body">
                        <ul class="mb-3">
                            <li>45 minutes per session</li>
                            <li>3 sessions per week</li>
                            <li>Focus on Tajweed rules</li>
                            <li>Quranic understanding</li>
                        </ul>
                        <h4 class="text-center text-success">$80 / month</h4>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ url('/contact') }}" class="btn btn-primary">Enroll Now</a>
                    </div>
                </div>
            </div>

            <!-- Package 3 -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white text-center fs-5 fw-semibold">
                        Advanced Package
                    </div>
                    <div class="card-body">
                        <ul class="mb-3">
                            <li>60 minutes per session</li>
                            <li>5 sessions per week</li>
                            <li>Memorization (Hifz)</li>
                            <li>Advanced Tajweed and Tafseer</li>
                        </ul>
                        <h4 class="text-center text-success">$120 / month</h4>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ url('/contact') }}" class="btn btn-primary">Enroll Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
