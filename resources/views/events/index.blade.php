@extends('layouts.layoutMaster')
@section('title', 'Events')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Events</h5>
            <a href="{{ route('events.create') }}" class="add-new btn btn-md float-end btn-primary waves-effect waves-light">
                <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                <span class="d-none d-sm-inline-block">Add New Event</span>
            </a>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">location</th>
                            <th class="fw-bold">Event Start Date</th>
                            <th class="fw-bold">Event End Date</th>
                            <th class="fw-bold">Booking Start Date</th>
                            <th class="fw-bold">Booking End Date</th>
                            <th class="fw-bold">Attendees</th>
                            <th class="text-center fw-bold">URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($events as $event)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ \Illuminate\Support\Str::title($event->name) }}</td>
                                <td>{{ $event->location }}</td>
                                <td>{{ $event->event_start_date?->format('d/m/Y h:i A') }}</td>
                                <td>{{ $event->event_end_date?->format('d/m/Y h:i A') }}</td>
                                <td>{{ $event->booking_start_date?->format('d/m/Y h:i A') }}</td>
                                <td>{{ $event->booking_end_date?->format('d/m/Y h:i A') }}</td>
                                <td>{{ $event->attendees_count }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <button class="btn btn-secondary btn-sm"
                                            onclick="copyToClipboard('{{ route('events.registration', $event->event_code) }}')">
                                            <i class="fa fa-clipboard"></i>
                                        </button>
                                    </div>
                                </td>

                                {{-- <td class="text-center">
                                    <a href="{{ route('changeStatus', ['model' => 'Event', 'id' => $event->id]) }}"
                                        class="btn btn-primary btn-sm">{{ $event->status == 1 ? 'Active' : 'InActive' }}</a>
                                </td> --}}
                                {{-- <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#deletecourse-{{ $course->id }}"> <i class="fa fa-trash"></i>
                                    </button>
                                </td> --}}
                            </tr>
                            <!--edit modal closed-->
                            <!--delete modal start-->
                            {{-- <div class="modal fade" id="deletecourse-{{ $course->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Delete
                                                    {{ \Illuminate\Support\Str::title($course->name) }}</h4>
                                            </div>
                                            <form class="row g-6 form" action="{{ route('course.destroy', $course->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('DELETE')
                                                <div class="col-12 col-md-12 mb-2">
                                                    Are you you want to delete this course?
                                                </div>

                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" class="btn btn-danger me-3 submitButton">Yes,
                                                        delete
                                                        it!</button>
                                                    <button type="reset" class="btn btn-label-secondary"
                                                        data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <!--delete closed-->
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
<script>
    function copyToClipboard(text) {
        // Create temporary input
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        try {
            document.execCommand("copy");
            alert("Link copied to clipboard!");
        } catch (err) {
            console.error("Fallback copy failed", err);
        }

        document.body.removeChild(tempInput);
    }
</script>
