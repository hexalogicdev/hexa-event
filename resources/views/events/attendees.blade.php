@extends('layouts.layoutMaster')
@section('title', 'Events')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Attendees</h5>
            <a href="{{ route('events.index') }}"
                class="add-new btn btn-md float-end btn-primary waves-effect waves-light ml-2 m-2">
                <span class="d-none d-sm-inline-block">Back</span>
            </a>
            <a href="#" class="add-new btn btn-md float-end btn-primary waves-effect waves-light m-2">
                <span class="d-none d-sm-inline-block">Export CSV</span>
            </a>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">Email</th>
                            <th class="fw-bold">Contact</th>
                            <th class="fw-bold">Check In Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($attendees as $attendee)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $attendee->name }}</td>
                                <td>{{ $attendee->email }}</td>
                                <td>{{ $attendee->phone }}</td>
                                <td>{{$attendee->checked_in_at ?? 'N/A'}}</td>
                            </tr>

                            <!--delete closed-->
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
