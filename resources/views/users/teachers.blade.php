@extends('layouts.layoutMaster')
@section('title', 'Teachers')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Teachers</h5>
            <button class="add-new btn btn-md float-end btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasAddUser">
                <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                <span class="d-none d-sm-inline-block">Add New Teacher</span>
            </button>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">User</th>
                            <th class="fw-bold">Email</th>
                            <th class="text-center fw-bold">Status</th>
                            <th class="text-center fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($teachers as $employee)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ \Illuminate\Support\Str::title($employee->name) }}</td>
                                <td>{{ $employee->email }}</td>
                                <td class="text-center">
                                    <a href="{{ route('changeStatus', ['model' => 'User', 'id' => $employee->id]) }}"
                                        class="btn btn-primary btn-sm">{{ $employee->status == 1 ? 'Active' : 'InActive' }}</a>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#editEmployee-{{ $employee->id }}"> <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#deleteEmployee-{{ $employee->id }}"> <i class="fa fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#changePassword-{{ $employee->id }}"> <i class="fa fa-unlock"
                                            aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            <div class="modal fade" id="editEmployee-{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Edit Teacher</h4>
                                            </div>
                                            <form class="row g-6 form" action="{{ route('User.update', $employee->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="id" value="{{ $employee->id }}">
                                                <div class="col-12 col-md-12 mb-2">
                                                    <label class="form-label" for="name">Name <i
                                                            class="text-danger">*</i></label>
                                                    <input type="text" id="edit_name" name="name" class="form-control"
                                                        placeholder="John" value="{{ $employee->name }}" required />
                                                    @if ($errors->editEmployee->has('name') && $employee->id == old('id'))
                                                        <span
                                                            class="text-danger">{{ $errors->editEmployee->first('name') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-12">
                                                    <label class="form-label" for="email">Email <i
                                                            class="text-danger">*</i></label>
                                                    <input type="email" id="edit_email" name="email"
                                                        class="form-control" placeholder="" value="{{ $employee->email }}"
                                                        required />
                                                    @if ($errors->editEmployee->has('email') && $employee->id == old('id'))
                                                        <span
                                                            class="text-danger">{{ $errors->editEmployee->first('email') }}</span>
                                                    @endif
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
                            <div class="modal fade" id="deleteEmployee-{{ $employee->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Delete
                                                    {{ \Illuminate\Support\Str::title($employee->name) }}</h4>
                                            </div>
                                            <form class="row g-6 form" action="{{ route('User.destroy', $employee->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('DELETE')
                                                <div class="col-12 col-md-12 mb-2">
                                                    Are you you want to delete this Teacher?
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
                            <!--change password start-->
                            <div class="modal fade" id="changePassword-{{ $employee->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Update Password for
                                                    {{ \Illuminate\Support\Str::title($employee->name) }}</h4>
                                            </div>
                                            <div class="row">
                                                <span class="mb-2 mt-2">
                                                    Password must be at least 8 characters long, contain at least one
                                                    uppercase
                                                    letter one lowecase letter and one special character (e.g., !@#$%^&*)
                                                </span>
                                            </div>
                                            <form class="row g-6 form" action="{{ route('User.update', $employee->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="type" value="change_password">
                                                <input type="hidden" name="id" value="{{ $employee->id }}">
                                                <div class="col-12 col-md-12 mb-2 mt-2">
                                                    <label class="form-label" for="password-{{ $employee->id }}">Password
                                                        <i class="text-danger">*</i></label>
                                                    <input type="password" id="password-{{ $employee->id }}"
                                                        name="password" class="form-control" minlength="8"
                                                        placeholder="Password must be at least 8 characters long, contain at least one uppercase letter and one special character (e.g., !@#$%^&*)"
                                                        required />
                                                    @if ($errors->change_password->has('password') && $employee->id == old('id'))
                                                        <span
                                                            class="text-danger">{{ $errors->change_password->first('password') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-12">
                                                    <label class="form-label"
                                                        for="password_confirmation-{{ $employee->id }}">Confirm Password
                                                        <i class="text-danger">*</i></label>
                                                    <input type="password" id="password_confirmation-{{ $employee->id }}"
                                                        name="password_confirmation" minlength="8" class="form-control"
                                                        placeholder="Password must be at least 8 characters long, contain at least one uppercase letter and one special character (e.g., !@#$%^&*)"
                                                        required />
                                                    @if ($errors->change_password->has('password_confirmation') && $employee->id == old('id'))
                                                        <span
                                                            class="text-danger">{{ $errors->change_password->first('password_confirmation') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit"
                                                        class="submitButton btn btn-primary me-3">Update</button>
                                                    <button type="reset" class="btn btn-label-secondary"
                                                        data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Offcanvas to add new user -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser"
                aria-labelledby="offcanvasAddUserLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Teacher</h5>
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
