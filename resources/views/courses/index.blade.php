@extends('layouts.layoutMaster')
@section('title', 'Courses')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Courses</h5>
            <button class="add-new btn btn-md float-end btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasAddUser">
                <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                <span class="d-none d-sm-inline-block">Add New Course</span>
            </button>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">Id</th>
                            <th class="fw-bold">Title</th>
                            <th class="fw-bold">File</th>
                            <th class="text-center fw-bold">Course</th>
                            <th class="text-center fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($courses as $course)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ \Illuminate\Support\Str::title($course->title) }}</td>
                                <td>{{ $course->attachment }}</td>
                                <td class="text-center">
                                    <a href="{{ route('changeStatus', ['model' => 'Course', 'id' => $course->id]) }}"
                                        class="btn btn-primary btn-sm">{{ $course->status == 1 ? 'Active' : 'InActive' }}</a>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm mx-1 my-1" data-bs-toggle="modal"
                                        data-bs-target="#deletecourse-{{ $course->id }}"> <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <!--edit modal closed-->
                            <!--delete modal start-->
                            <div class="modal fade" id="deletecourse-{{ $course->id }}" tabindex="-1" aria-hidden="true">
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
                    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Course</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                    <form class="add-new-user pt-0 form" action="{{ route('course.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="user_id">
                        <div class="mb-6">
                            <label class="form-label" for="name">Title<i class="text-danger">*</i></label>
                            <input type="text" class="form-control" id="name" placeholder="Ttile" name="title"
                                value="{{ old('title') }}" minlength="3" maxlength="20" aria-label="Title" required />
                            @if ($errors->add_course->has('title'))
                                <div class="text-danger">{{ $errors->add_course->first('title') }}</div>
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

</script>
