@extends('layouts.layoutMaster')
@section('title', 'Events')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Events</h5>
            <a href="{{ route('events.index') }}" class="add-new btn btn-md float-end btn-primary waves-effect waves-light">
                <span class="d-none d-sm-inline-block">Back</span>
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label for="">Event Name</label>
                    <input type="text" class="form-control" name="name" placeholder="Enter event name"
                        value="{{ old('name') }}" required>
                </div>
                <div class="mb-2">
                    <label for="">Description</label>
                    <textarea class="form-control" name="description" placeholder="Enter event description"
                        value="{{ old('description') }}"></textarea>
                </div>
                <div class="mb-2">
                    <label for="">Location</label>
                    <input type="text" class="form-control" name="location" placeholder="Enter event location"
                        value="{{ old('location') }}" required>
                </div>
                <div class="mb-2">
                    <label for="">Event Start Date</label>
                    <input type="date" class="form-control" name="event_start_date" value="{{ old('event_start_date') }}"
                        required>
                </div>
                <div class="mb-2">
                    <label for="">Event End Date</label>
                    <input type="date" class="form-control" name="event_end_date" value="{{ old('event_end_date') }}"
                        required>
                </div>
                <div class="mb-2">
                    <label for="">Booking Start Date</label>
                    <input type="date" class="form-control" name="booking_start_date"
                        value="{{ old('booking_start_date') }}" required>
                </div>
                <div class="mb-2">
                    <label for="">Booking End Date</label>
                    <input type="date" class="form-control" name="booking_end_date" value="{{ old('booking_end_date') }}"
                        required>
                </div>
                <div class="mb-2">
                    <label for="">Max Attendees</label>
                    <input type="number" class="form-control" name="max_attendees" value="{{ old('max_attendees') }}"
                        placeholder="Enter max attendees" required min="1">
                </div>
                <div class="mb-2">
                    <label for="">Check-in Password</label>
                    <input type="text" class="form-control" name="checkin_password"
                        value="{{ old('checkin_password') }}" minlength="3" maxlength="5"
                        placeholder="Enter check-in password" required>
                </div>
                <div class="mb-2">
                    <label for="">Price</label>
                    <input type="number" class="form-control" name="price" placeholder="Enter event price "
                        value="{{ old('price') }}" min="0" max="10000" required>
                </div>
                <button class="btn btn-primary" type="submit">Submit</button>
            </form>
        </div>
    @endsection

    </script>
