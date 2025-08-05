@extends('layouts.layoutMaster')
@section('title', 'Add Ons')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 float-start">Add-Ons</h5>
            <button class="add-new btn btn-md float-end btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                data-bs-target="#addNewCustomRecord">
                <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                <span class="d-none d-sm-inline-block">Add New Add-On</span>
            </button>
        </div>
        <div class="card-body">
            <div class="card-datatable text-nowrap table-responsive">
                <table class="datatables-ajax table">
                    <thead class="border-top">
                        <tr>
                            <th class="fw-bold">ID</th>
                            <th class="fw-bold">Name</th>
                            {{-- <th class="fw-bold">Description</th> --}}
                            {{-- <th class="fw-bold">Start Time</th> --}}
                            {{-- <th class="fw-bold">End Time</th> --}}
                            <th class="fw-bold">Price </th>
                            <th class="fw-bold">Package </th>
                            <th class="fw-bold text-center">Status</th>
                            <th class="fw-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($addOns as $addon)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ \Illuminate\Support\Str::title($addon->name) }}</td>
                                {{-- <td>{{ $addon->description }}</td> --}}
                                {{-- <td>{{ $addon->start_time?->format('h:i A') }}</td> --}}
                                {{-- <td>{{ $addon->end_time?->format('h:i A') }}</td> --}}
                                <td>€ {{ $addon->price }}</td>
                                <td>{{ $addon->is_package == 1 ? 'Yes' : 'No' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('changeStatus', ['model' => 'AddOn', 'id' => $addon->id]) }}"
                                        class="btn btn-primary btn-sm">{{ $addon->status == 1 ? 'Active' : 'InActive' }}</a>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editAddOn-{{ $addon->id }}"> Edit </button>
                                </td>
                            </tr>
                            <!-- Edit addon Modal -->
                            <div class="modal fade" id="editAddOn-{{ $addon->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="text-center pb-6">
                                                <h4 class="mb-4">Edit Add-On</h4>
                                            </div>
                                            <form class="row g-6 form" id="form"
                                                action="{{ route('addOn.update', $addon->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')

                                                <div class="row mb-2">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label" for="name">Name <i
                                                                class="text-danger">*</i></label>
                                                        <input type="text" id="name" name="name" required
                                                            class="form-control @error('name', 'editAddOn_' . $addon->id) is-invalid @enderror"
                                                            placeholder="Name" value="{{ old('name', $addon->name) }}" />
                                                        @error('name', 'editAddOn_' . $addon->id)
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label" for="price">Price (€)<i
                                                                class="text-danger">*</i></label>
                                                        <input type="text" id="price" name="price" required
                                                            class="form-control @error('price', 'editAddOn_' . $addon->id) is-invalid @enderror"
                                                            placeholder="00.00"
                                                            value="{{ old('price', $addon->price) }}" />
                                                        @error('price', 'editAddOn_' . $addon->id)
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- <div class="row mb-2">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label" for="start_time">Start Time</label>
                                                        <input type="time" id="start_time" name="start_time"
                                                            class="form-control @error('start_time', 'editAddOn_' . $addon->id) is-invalid @enderror"
                                                            value="{{ old('start_time', $addon->start_time ? \Carbon\Carbon::parse($addon->start_time)->format('H:i') : '') }}" />
                                                        @error('start_time', 'editAddOn_' . $addon->id)
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label" for="end_time">End Time</label>
                                                        <input type="time" id="end_time" name="end_time"
                                                            class="form-control @error('end_time', 'editAddOn_' . $addon->id) is-invalid @enderror"
                                                            value="{{ old('end_time', $addon->end_time ? \Carbon\Carbon::parse($addon->end_time)->format('H:i') : '') }}" />
                                                        @error('end_time', 'editAddOn_' . $addon->id)
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                                <div class="row mb-2">
                                                    {{-- <div class="col-12 col-md-12 mb-2">
                                                        <label class="form-label" for="description">Description</label>
                                                        <textarea name="description" class="form-control @error('description', 'editAddOn_' . $addon->id) is-invalid @enderror"
                                                            cols="30" rows="5" placeholder="Description">{{ old('description', $addon->description) }}</textarea>
                                                        @error('description', 'editAddOn_' . $addon->id)
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div> --}}
                                                    <div class="col-12 col-md-12 mb-2 mt-3">
                                                      <div class="form-check">
                                                          <input class="form-check-input" type="checkbox" id="is_package"
                                                              name="is_package"
                                                              {{ old('is_package', $addon->is_package) ? 'checked' : '' }} />
                                                          <label class="form-check-label" for="is_package">This Add-On is only for Package?</label>
                                                      </div>
                                                  </div>
                                                </div>
                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" id="submitButton"
                                                        class="btn btn-primary me-3 submit-button">Update</button>
                                                    <button type="reset" class="btn btn-label-secondary"
                                                        data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/ Edit addon Modal -->
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Offcanvas to add new addon -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="addNewCustomRecord"
                aria-labelledby="addNewCustomRecordLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="addNewCustomRecordLabel" class="offcanvas-title">Add New Add-On</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                    <form class="add-custom-record pt-0 form" id="form" action="{{ route('addOn.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label" for="custom-name">Name <i class="text-danger">*</i></label>
                            <input type="text" minlength="3" maxlength="50"
                                class="form-control @error('name', 'addNewCustomRecord') is-invalid @enderror"
                                id="custom-name" placeholder="Name" name="name" aria-label="Name"
                                value="{{ old('name') }}" required />
                            @error('name', 'addNewCustomRecord')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="mb-6">
                            <label class="form-label" for="start_time">Start Time</label>
                            <input type="time" id="start_time"
                                class="form-control @error('start_time', 'addNewCustomRecord') is-invalid @enderror"
                                placeholder="00:00" aria-label="start_time" name="start_time"
                                value="{{ old('start_time') }}" />
                            @error('start_time', 'addNewCustomRecord')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="end_time">End Time</label>
                            <input type="time" id="end_time"
                                class="form-control @error('end_time', 'addNewCustomRecord') is-invalid @enderror"
                                placeholder="00:00" aria-label="end_time" name="end_time" value="{{ old('end_time') }}"
                                 />
                            @error('end_time', 'addNewCustomRecord')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="mb-6">
                            <label class="form-label" for="price">Price (€) <i class="text-danger">*</i></label>
                            <input type="text" id="price"
                                class="form-control @error('price', 'addNewCustomRecord') is-invalid @enderror"
                                placeholder="00.00" aria-label="price" name="price" min="1" max="500"
                                step="0.01" value="{{ old('price') }}" required />
                            @error('price', 'addNewCustomRecord')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="mb-6">
                            <label class="form-label" for="description">Description</label>
                            <textarea name="description" class="form-control @error('description', 'addNewCustomRecord') is-invalid @enderror"
                                cols="30" rows="7" placeholder="Description" maxlength="500" >{{ old('description') }}</textarea>
                            @error('description', 'addNewCustomRecord')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="mb-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_package" name="is_package"
                                    {{ old('is_package') ? 'checked' : '' }} />
                                <label class="form-check-label" for="is_package">This Add-On is only for Package?</label>
                            </div>
                        </div>

                        <button type="submit" id="submitButton"
                            class="btn btn-primary me-3 data-submit submit-button">Submit</button>
                        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            @if ($errors->getBag('addNewCustomRecord')->any())
                var addNewCustomRecord = new bootstrap.Offcanvas(document.getElementById('addNewCustomRecord'));
                addNewCustomRecord.show();
            @endif
        });
        @if (isset($addon) && $addon)
            document.addEventListener("DOMContentLoaded", function() {
                @if ($errors->getBag('editAddOn_' . $addon?->id)->any())
                    var editAddOnModal = new bootstrap.Modal(document.getElementById(
                        'editAddOn-{{ $addon->id }}'));
                    editAddOnModal.show();
                @endif
            });
        @endif
    </script>
@endsection
