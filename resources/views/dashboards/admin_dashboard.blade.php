@extends('layouts.layoutMaster')
@section('title', 'Dashboard')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/app-calendar.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/moment/moment.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app-calendar-events.js', 'resources/assets/js/app-calendar.js'])
@endsection
@section('content')
    <div class="card">
        <div class="col-12">
            <div class="card-header border-bottom text-center"
                style="background: linear-gradient(to right, #1db4eb 0%, #9cbf17 80%) !important;">
                <h5 class="card-title mb-0 text-white">Welcome to Event Management</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="col-12">
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mt-3 mb-2">
                        <div class="card h-100 shadow border-0  mb-4">
                            <div class="card-body py-3">
                                <h5 class="mb-3 mt-2 card-title">Total Events</h5>
                                <h4 class="mb-0">{{ $eventsCount }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mt-2 mb-2">
                        <div class="card h-100 shadow border-0 mb-4">
                            <div class="card-body py-3">
                                <h5 class="mb-3 mt-2 card-title">Total Attendees</h5>
                                <h4 class="mb-0">{{ $attendees }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mt-2 mb-2">
                        <div class="card h-100 shadow border-0 mb-4">
                            <div class="card-body py-3">
                                <h5 class="mb-3 mt-2 card-title">Total Revenue</h5>
                                <h4 class="mb-0">{{ $revenue }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Events</h5>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">Event Date</th>
                            <th class="fw-bold">Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($events as $event)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $event->name }}</td>
                                <td>{{ $event->event_start_date?->format('d/m/Y') }}</td>
                                <td>{{ $event->location }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@endsection
