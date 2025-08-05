@extends('dome_carlow.layouts.main')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    <style>
        .header-steps {
            display: none;
        }
    </style>
    <div id="wrapper">
        @include('dome_carlow.layouts.header')
        <div class="container">
            <div class="row d-flex justify-content-center align-items-center" style="height: 90vh">
                <div class="col-12 col-sm-8 col-md-6 col-lg-6">
                    <div class="card shadow">
                        <div class="card-body text-center py-4">
                            <i class="fa fa-check-circle" aria-hidden="true" style="font-size:70px;color:green"></i>
                            <h3 class="mt-3 mb-1">Thank you for booking with us!</h3>
                            <p class="mb-0">Have more insight to share or questions?</p>
                            <p> Our Support team is here to help</p>

                            <a href="{{route('dome-carlow.index')}}" class=" cta text-white  main-continue text-decoration-none px-5 py-3 my-3">Home</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('dome_carlow.layouts.footer')

    </div>
@endsection
