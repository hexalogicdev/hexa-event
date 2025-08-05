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
                <h5 class="card-title mb-0 text-white">Welcome to Darulquran</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="col-12">
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mt-2 mb-2">
                        <div class="card h-100 shadow border-0 mb-4">
                            <div class="card-body py-3">
                                <h5 class="mb-3 mt-2 card-title">Total Students</h5>
                                <h4 class="mb-0">{{ $students->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card app-calendar-wrapper">
                <div class="row g-0">
                    <!-- Calendar Sidebar -->
                    <div class="col app-calendar-sidebar border-end d-none" id="app-calendar-sidebar">
                        <input class="form-check-input input-filter" type="checkbox" id="select-business"
                            data-value="business" checked>
                    </div>
                    <!-- /Calendar Sidebar -->

                    <!-- Calendar & Modal -->
                    <div class="col app-calendar-content">
                        <div class="card shadow-none border-0">
                            <div class="card-body pb-0">
                                <!-- FullCalendar -->
                                <div id="calendar"></div>
                            </div>
                        </div>
                        <div class="app-overlay"></div>
                        <!-- FullCalendar Offcanvas -->
                        <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                            aria-labelledby="addEventSidebarLabel">
                            <div class="offcanvas-header border-bottom">
                                <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                    <div class="mb-5">
                                        <label class="form-label" for="eventTitle">Title</label>
                                        <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                                            placeholder="Event Title" />
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label" for="eventLabel">Label</label>
                                        <select class="select2 select-event-label form-select" id="eventLabel"
                                            name="eventLabel">
                                            <option data-label="primary" value="Business" selected>Business</option>
                                            <option data-label="danger" value="Personal">Personal</option>
                                            <option data-label="warning" value="Family">Family</option>
                                            <option data-label="success" value="Holiday">Holiday</option>
                                            <option data-label="info" value="ETC">ETC</option>
                                        </select>
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label" for="eventStartDate">Start Date</label>
                                        <input type="text" class="form-control" id="eventStartDate" name="eventStartDate"
                                            placeholder="Start Date" />
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label" for="eventEndDate">End Date</label>
                                        <input type="text" class="form-control" id="eventEndDate" name="eventEndDate"
                                            placeholder="End Date" />
                                    </div>
                                    <div class="mb-5">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input allDay-switch"
                                                id="allDaySwitch" />
                                            <label class="form-check-label" for="allDaySwitch">All Day</label>
                                        </div>
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label" for="eventURL">Event URL</label>
                                        <input type="url" class="form-control" id="eventURL" name="eventURL"
                                            placeholder="https://www.google.com" />
                                    </div>
                                    <div class="mb-4 select2-primary">
                                        <label class="form-label" for="eventGuests">Add Guests</label>
                                        <select class="select2 select-event-guests form-select" id="eventGuests"
                                            name="eventGuests" multiple>
                                            <option data-avatar="1.png" value="Jane Foster">Jane Foster</option>
                                            <option data-avatar="3.png" value="Donna Frank">Donna Frank</option>
                                            <option data-avatar="5.png" value="Gabrielle Robertson">Gabrielle Robertson
                                            </option>
                                            <option data-avatar="7.png" value="Lori Spears">Lori Spears</option>
                                            <option data-avatar="9.png" value="Sandy Vega">Sandy Vega</option>
                                            <option data-avatar="11.png" value="Cheryl May">Cheryl May</option>
                                        </select>
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label" for="eventLocation">Location</label>
                                        <input type="text" class="form-control" id="eventLocation"
                                            name="eventLocation" placeholder="Enter Location" />
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label" for="eventDescription">Description</label>
                                        <textarea class="form-control" name="eventDescription" id="eventDescription"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-sm-between justify-content-start mt-6 gap-2">
                                        <div class="d-flex">
                                            <button type="submit" id="addEventBtn"
                                                class="btn btn-primary btn-add-event me-4">Add</button>
                                            <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                                data-bs-dismiss="offcanvas">Cancel</button>
                                        </div>
                                        <button class="btn btn-label-danger btn-delete-event d-none">Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Upcoming Classes</h5>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Child</th>
                            <th class="fw-bold">Teacher</th>
                            <th class="fw-bold">Date</th>
                            <th class="fw-bold">Time</th>
                            <th class="fw-bold">Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($classes as $class)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ \Illuminate\Support\Str::title($class->student->name) }}</td>
                                <td>{{ $class->teacher->name }}</td>
                                <td>{{ $class->date?->format('d/m/Y') }}</td>
                                <td>{{ $class?->start_time?->format('h:i A') }}</td>
                                <td>{{ $class->meeting_link }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="viewBookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-edit-user">
            <div class="modal-content p-0">
                <div class="modal-header border py-3">
                    <h5 class="mb-0">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3" id="bookingModalBody">

                </div>
                <div class="modal-footer pb-3">
                    <div class="col-12 text-end mt-3">
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // $(document).on('click', '.view-booking', function() {
        //     let bookingId = $(this).data('id');
        //     $.ajax({
        //         url: '/booking/' + bookingId,
        //         method: 'GET',
        //         beforeSend: function() {
        //             $('#bookingModalBody').html(
        //                 '<p>Loading booking details...</p>'); // Show loading text
        //         },
        //         success: function(data) {
        //             // Assuming the response is HTML or JSON
        //             $('#bookingModalBody').html(data.html);
        //             $('#viewBookingModal').modal('show'); // Show the modal
        //         },
        //         error: function(xhr, status, error) {
        //             console.error("Error loading booking details:", error);
        //             $('#bookingModalBody').html(
        //                 '<p class="text-danger">Failed to load booking details. Try again later.</p>'
        //             );
        //         }
        //     });
        // });
    </script>
@endsection
