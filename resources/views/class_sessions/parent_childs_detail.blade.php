@extends('layouts.layoutMaster')
@section('title', 'Classes')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Classes</h5>
            <a href="{{ route('student.index') }}" class="add-new btn btn-md float-end btn-primary waves-effect waves-light">
                <span class="d-none d-sm-inline-block">Back</span>
            </a>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Teacher</th>
                            <th class="fw-bold">Date</th>
                            <th class="fw-bold">Day</th>
                            <th class="fw-bold">Time</th>
                            <th class="fw-bold">Status</th>
                            <th class="fw-bold">Teacher Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($classes as $class)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $student->teacher->name }}</td>
                                <td>{{ $class->date?->format('d/m/Y') }}</td>
                                <td>{{ $class->date?->format('l') }}</td>
                                <td>{{ $class->time?->format('h:i A') }}</td>
                                <td>{{ $class->status }}</td>
                                <td>{{ $class->teacher_remarks }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

</script>
