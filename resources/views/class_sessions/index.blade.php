@extends('layouts.layoutMaster')
@section('title', 'Classes')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Classes</h5>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Student</th>
                            <th class="fw-bold">Teacher</th>
                            <th class="fw-bold">Date</th>
                            <th class="fw-bold">Day</th>
                            <th class="fw-bold">Time</th>
                            <th class="fw-bold">Link</th>
                            <th class="fw-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($sessions as $session)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $session->student->name }}</td>
                                <td>{{ $session->teacher->name }}</td>
                                <td>{{ $session->date?->format('d/m/Y') }}</td>
                                <td>{{ $session->date?->format('l') }}</td>
                                <td>{{ $session->start_time?->format('h:i A') }}</td>
                                <td></td>
                                <td>{{ $session->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

</script>
