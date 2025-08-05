@extends('layouts.layoutMaster')
@section('title', 'Childrens')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Childrens</h5>
            @if (auth()->check() && auth()->user()->role?->name === 'parent')
                <button class="add-new btn btn-md float-end btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasAddUser">
                    <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                    <span class="d-none d-sm-inline-block">Add New Children</span>
                </button>
            @endif
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">Days</th>
                            <th class="fw-bold">Time</th>
                            <th class="fw-bold">Teacher</th>
                            <th class="fw-bold">Course</th>
                            <th class="text-center fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ \Illuminate\Support\Str::title($student->name) }}</td>
                                <td class="text-center text-wrap">{{ implode(', ', json_decode($student?->days)) }}</td>
                                <td>{{ $student->start_time?->format('h:i A') }}</td>
                                <td>{{ $student->teacher?->name }}</td>
                                <td>{{ $student->course?->title }}</td>
                                <td class="text-center">
                                    @if (auth()->check() && auth()->user()->role?->name === 'parent')
                                        <button type="button" class="btn btn-primary btn-sm mx-1 my-1"
                                            data-bs-toggle="modal" data-bs-target="#deleteEmployee-{{ $student->id }}"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('student.show', $student->id) }}" title="Classes"
                                        class="btn btn-primary btn-sm mx-1 my-1"> <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <!--edit modal closed-->
                            <!--delete modal start-->
                            <div class="modal fade" id="deleteEmployee-{{ $student->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Delete
                                                    {{ \Illuminate\Support\Str::title($student->name) }}</h4>
                                            </div>
                                            <form class="row g-6 form"
                                                action="{{ route('student.destroy', $student->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('DELETE')
                                                <div class="col-12 col-md-12 mb-2">
                                                    Are you you want to delete this child?
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
                            </div>
                            <!--delete closed-->
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Offcanvas to add new user -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser"
                aria-labelledby="offcanvasAddUserLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Children</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                    <form class="add-new-user pt-0 form" action="{{ route('student.store') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label" for="name">Full Name <i class="text-danger">*</i></label>
                            <input type="text" class="form-control" id="name" placeholder="Full Name" name="name"
                                value="{{ old('name') }}" minlength="3" maxlength="20" aria-label="Full Name"
                                required />
                            @if ($errors->add_employee->has('name'))
                                <div class="text-danger">{{ $errors->add_employee->first('name') }}</div>
                            @endif
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="email">Language <i class="text-danger">*</i></label>
                            <select id="selectpickerSelectDeselect" class="selectpicker form-select w-100"
                                name="languages[]" placeholder="Select Languages" multiple required data-style="btn-default"
                                data-actions-box="true">
                                <option value="English" {{ in_array('English', old('languages', [])) ? 'selected' : '' }}>
                                    English
                                </option>
                                <option value="Urdu" {{ in_array('Urdu', old('languages', [])) ? 'selected' : '' }}>
                                    Urdu
                                </option>
                                <option value="Punjabi" {{ in_array('Punjabi', old('languages', [])) ? 'selected' : '' }}>
                                    Punjabi
                                </option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="email">Teacher Preference <i
                                    class="text-danger">*</i></label>
                            <select id="selectpickerSelectDeselect" class="selectpicker form-select w-100"
                                name="teacher_prefernece" placeholder="Select Teacher Preference" required
                                data-style="btn-default" data-actions-box="true">
                                <option value="Male" {{ in_array('Male', old('teacher', [])) ? 'selected' : '' }}>
                                    Male
                                </option>
                                <option value="Female" {{ in_array('Female', old('teacher', [])) ? 'selected' : '' }}>
                                    Female
                                </option>
                                <option value="Both" {{ in_array('Both', old('teacher', [])) ? 'selected' : '' }}>
                                    Both
                                </option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="email">Days <i class="text-danger">*</i></label>
                            <select id="selectpickerSelectDeselect" class="selectpicker form-select w-100" name="days[]"
                                placeholder="Select Days" multiple required data-style="btn-default"
                                data-actions-box="true">
                                <option value="Monday" {{ in_array('Monday', old('days', [])) ? 'selected' : '' }}>
                                    Monday
                                </option>
                                <option value="Tuesday" {{ in_array('Tuesday', old('days', [])) ? 'selected' : '' }}>
                                    Tuesday
                                </option>
                                <option value="Wednesday" {{ in_array('Wednesday', old('days', [])) ? 'selected' : '' }}>
                                    Wednesday
                                </option>
                                <option value="Thursday" {{ in_array('Thursday', old('days', [])) ? 'selected' : '' }}>
                                    Thursday
                                </option>
                                <option value="Friday" {{ in_array('Friday', old('days', [])) ? 'selected' : '' }}>
                                    Friday
                                </option>
                                <option value="Saturday" {{ in_array('Saturday', old('days', [])) ? 'selected' : '' }}>
                                    Saturday
                                </option>
                                <option value="Sunday" {{ in_array('Sunday', old('days', [])) ? 'selected' : '' }}>
                                    Sunday
                                </option>
                            </select>
                            @if ($errors->add_employee->has('days'))
                                <div class="text-danger">{{ $errors->add_employee->first('days') }}</div>
                            @endif
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="password">Start Time <i class="text-danger">*</i></label>
                            <input type="time" id="start_time" class="form-control" minlength="8"
                                aria-label="start_time" name="start_time" required />
                            @if ($errors->add_employee->has('start_time'))
                                <div class="text-danger">{{ $errors->add_employee->first('start_time') }}</div>
                            @endif
                        </div>
                        <button type="submit"
                            class="submitButton btn btn-primary me-3 data-submit submit-button">Submit</button>
                        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->hasBag('add_employee'))
            var offcanvasAddEmployee = new bootstrap.Offcanvas(document.getElementById(
                'offcanvasAddUser'));
            offcanvasAddEmployee.show();
        @endif
    });
    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->hasBag('change_password'))
            var employeeModalId = "changePassword-{{ session('show_modal') }}";
            var changePasswordModal = new bootstrap.Modal(document.getElementById(employeeModalId));
            changePasswordModal.show();
        @endif
    });
    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->hasBag('editEmployee'))
            // Get the modal ID from the session variable
            var employeeModalId = "editEmployee-{{ session('show_modal') }}";
            var editEmployeeModal = new bootstrap.Modal(document.getElementById(employeeModalId));
            editEmployeeModal.show();
        @endif
    });
</script>
</script>
