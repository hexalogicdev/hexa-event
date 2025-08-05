@extends('layouts.layoutMaster')
@section('title', 'Students')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Students</h5>
            {{-- <button class="add-new btn btn-md float-end btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasAddUser">
                <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                <span class="d-none d-sm-inline-block">Add New Student</span>
            </button> --}}
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Name</th>
                            <th class="fw-bold">Parent</th>
                            <th class="fw-bold">Teacher</th>
                            <th class="fw-bold">Course</th>
                            <th class="fw-bold">Language</th>
                            <th class="fw-bold">Days</th>
                            <th class="fw-bold">Start Time</th>
                            <th class="fw-bold">End Time</th>
                            <th class="fw-bold">Status</th>
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
                                <td>{{ $student->parent?->name }}</td>
                                <td>{{ $student->teacher?->name }}</td>
                                <td>{{ $student->course?->title }}</td>
                                <td class="text-center text-wrap">{{ implode(', ', json_decode($student?->language)) }}</td>
                                <td class="text-center text-wrap">{{ implode(', ', json_decode($student?->days)) }}</td>
                                <td>{{ $student->start_time?->format('h:i A') }}</td>
                                <td>{{ $student->end_time?->format('h:i A') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('changeStatus', ['model' => 'Student', 'id' => $student->id]) }}"
                                        class="btn btn-primary btn-sm">{{ $student->status == 1 ? 'Active' : 'InActive' }}</a>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#editEmployee-{{ $student->id }}"> <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#deleteEmployee-{{ $student->id }}"> <i class="fa fa-trash"></i>
                                    </button>
                                    <a href="{{ route('student.show', $student->id) }}" title="Classes"
                                        class="btn btn-primary btn-sm mx-1 my-1"> <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <div class="modal fade" id="editEmployee-{{ $student->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Edit Student</h4>
                                            </div>
                                            <form class="row g-6 form" action="{{ route('student.update', $student->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="id" value="{{ $student->id }}">

                                                <div class="col-12 col-md-12 mb-2">
                                                    <label class="form-label" for="edit_name">Name <i
                                                            class="text-danger">*</i></label>
                                                    <input type="text" id="edit_name" name="name" class="form-control"
                                                        placeholder="John" value="{{ old('name', $student->name) }}"
                                                        required>
                                                    @error('name')
                                                        @if ($student->id == old('id'))
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @endif
                                                    @enderror
                                                </div>

                                                <div class="col-12 col-md-12 mb-2">
                                                    <label class="form-label" for="teacher">Teacher <i
                                                            class="text-danger">*</i></label>
                                                    <select name="teacher" id="teacher" class="form-select">
                                                        <option value="">Select Teacher</option>
                                                        @foreach ($teachers as $teacher)
                                                            <option value="{{ $teacher->id }}"
                                                                {{ old('teacher', $student->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                                                {{ $teacher->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('teacher')
                                                        @if ($student->id == old('id'))
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @endif
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-12 mb-2">
                                                    <label class="form-label" for="teacher">Course <i
                                                            class="text-danger">*</i></label>
                                                    <select name="course" id="course" class="form-select">
                                                        <option value="">Select Course</option>
                                                        @foreach ($courses as $course)
                                                            <option value="{{ $course->id }}"
                                                                {{ old('course', $student->course_id) == $course->id ? 'selected' : '' }}>
                                                                {{ $course->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('course')
                                                        @if ($student->id == old('id'))
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @endif
                                                    @enderror
                                                </div>

                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" class="btn btn-primary me-3">Update</button>
                                                    <button type="reset" class="btn btn-label-secondary"
                                                        data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                    Are you you want to delete this Student?
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
                    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Student</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                    <form class="add-new-user pt-0 form" action="{{ route('User.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="user_id">
                        <div class="mb-6">
                            <label class="form-label" for="name">Full Name <i class="text-danger">*</i></label>
                            <input type="text" class="form-control" id="name" placeholder="Full Name"
                                name="name" value="{{ old('name') }}" minlength="3" maxlength="20"
                                aria-label="Full Name" required />
                            @if ($errors->add_employee->has('name'))
                                <div class="text-danger">{{ $errors->add_employee->first('name') }}</div>
                            @endif
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="email">Email <i class="text-danger">*</i></label>
                            <input type="email" id="email" class="form-control"
                                placeholder="employee@darulquran.com" aria-label="employee@doemcarlow.com" name="email"
                                value="{{ old('email') }}" autocomplete="new-email" required />
                            @if ($errors->add_employee->has('email'))
                                <div class="text-danger">{{ $errors->add_employee->first('email') }}</div>
                            @endif
                        </div>
                        <div class="col-12 col-md-12 mb-2 mt-2">
                            <span class="mb-2 text-danger">
                                Password must contain one uppercase , one lower and special characters (e.g., !@#$%^&*)
                            </span>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="password">Password <i class="text-danger">*</i></label>
                            <input type="password" id="new_password" class="form-control" minlength="8"
                                placeholder="Password must be at least 8 characters long" aria-label="new_Password"
                                name="password" autocomplete="new-password" required />
                            @if ($errors->add_employee->has('password'))
                                <div class="text-danger">{{ $errors->add_employee->first('password') }}</div>
                            @endif
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="password">Confirm Password <i
                                    class="text-danger">*</i></label>
                            <input type="password" id="password_confirmation" minlength="8"
                                name="password_confirmation" class="form-control"
                                placeholder="Password must be at least 8 characters long" required />
                            @if ($errors->add_employee->has('password_confirmation'))
                                <div class="text-danger">{{ $errors->add_employee->first('password_confirmation') }}</div>
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
