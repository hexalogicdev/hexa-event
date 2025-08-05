@extends('layouts.layoutMaster')
@section('title', 'Parents')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Parents</h5>
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
                            <th class="fw-bold">Country</th>
                            <th class="fw-bold">Address</th>
                            <th class="text-center fw-bold">Status</th>
                            <th class="text-center fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($parents as $parent)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ \Illuminate\Support\Str::title($parent->name) }}</td>
                                <td>{{ $parent->email }}</td>
                                <td>{{ $parent->contact }}</td>
                                <td>{{ $parent->country }}</td>
                                <td>{{ $parent->address }}</td>
                                <td class="text-center">
                                    <a href="{{ route('changeStatus', ['model' => 'User', 'id' => $parent->id]) }}"
                                        class="btn btn-primary btn-sm">{{ $parent->status == 1 ? 'Active' : 'InActive' }}</a>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#editEmployee-{{ $parent->id }}"> <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#deleteEmployee-{{ $parent->id }}"> <i class="fa fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#changePassword-{{ $parent->id }}"> <i class="fa fa-unlock"
                                            aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            <div class="modal fade" id="editEmployee-{{ $parent->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Edit Teacher</h4>
                                            </div>
                                            <form class="row g-6 form" action="{{ route('User.update', $parent->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="id" value="{{ $parent->id }}">
                                                <div class="col-12 col-md-12 mb-2">
                                                    <label class="form-label" for="name">Name <i
                                                            class="text-danger">*</i></label>
                                                    <input type="text" id="edit_name" name="name" class="form-control"
                                                        placeholder="John" value="{{ $parent->name }}" required />
                                                    @if ($errors->editEmployee->has('name') && $parent->id == old('id'))
                                                        <span
                                                            class="text-danger">{{ $errors->editEmployee->first('name') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-12">
                                                    <label class="form-label" for="email">Email <i
                                                            class="text-danger">*</i></label>
                                                    <input type="email" id="edit_email" name="email"
                                                        class="form-control" placeholder="" value="{{ $parent->email }}"
                                                        required />
                                                    @if ($errors->editEmployee->has('email') && $parent->id == old('id'))
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
                            <div class="modal fade" id="deleteEmployee-{{ $parent->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Delete
                                                    {{ \Illuminate\Support\Str::title($parent->name) }}</h4>
                                            </div>
                                            <form class="row g-6 form" action="{{ route('User.destroy', $parent->id) }}"
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
                            <div class="modal fade" id="changePassword-{{ $parent->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center mb-6">
                                                <h4 class="mb-2">Update Password for
                                                    {{ \Illuminate\Support\Str::title($parent->name) }}</h4>
                                            </div>
                                            <div class="row">
                                                <span class="mb-2 mt-2">
                                                    Password must be at least 8 characters long, contain at least one
                                                    uppercase
                                                    letter one lowecase letter and one special character (e.g., !@#$%^&*)
                                                </span>
                                            </div>
                                            <form class="row g-6 form"
                                                action="{{ route('student.update', $parent->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="type" value="change_password">
                                                <input type="hidden" name="id" value="{{ $parent->id }}">
                                                <div class="col-12 col-md-12 mb-2 mt-2">
                                                    <label class="form-label" for="password-{{ $parent->id }}">Password
                                                        <i class="text-danger">*</i></label>
                                                    <input type="password" id="password-{{ $parent->id }}"
                                                        name="password" class="form-control" minlength="8"
                                                        placeholder="Password must be at least 8 characters long, contain at least one uppercase letter and one special character (e.g., !@#$%^&*)"
                                                        required />
                                                    @if ($errors->change_password->has('password') && $parent->id == old('id'))
                                                        <span
                                                            class="text-danger">{{ $errors->change_password->first('password') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-12">
                                                    <label class="form-label"
                                                        for="password_confirmation-{{ $parent->id }}">Confirm Password
                                                        <i class="text-danger">*</i></label>
                                                    <input type="password" id="password_confirmation-{{ $parent->id }}"
                                                        name="password_confirmation" minlength="8" class="form-control"
                                                        placeholder="Password must be at least 8 characters long, contain at least one uppercase letter and one special character (e.g., !@#$%^&*)"
                                                        required />
                                                    @if ($errors->change_password->has('password_confirmation') && $parent->id == old('id'))
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
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
