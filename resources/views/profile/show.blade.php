@extends('layouts.layoutMaster')

@section('title', 'Profile')

@section('content')
    @php
        $user = $user ?? auth()->user();
    @endphp

    <div class="ems-dashboard-topbar">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            {{-- <img src="{{ asset('assets/img/hexa-logo.png') }}" alt="HexaLogicDev" class="ems-dashboard-brand"> --}}
            <div>
                <div class="ems-dashboard-kicker">Account center</div>
                <h1 class="ems-dashboard-title mb-0">Profile and password settings</h1>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
            <a href="{{ route('events.index') }}" class="btn btn-primary btn-sm">Events</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-5">
            <div class="ems-dashboard-card ems-dashboard-card--soft h-100">
                <div class="ems-dashboard-card__head">
                    <div>
                        <h2>Account summary</h2>
                        <p>Visible profile details for your workspace.</p>
                    </div>
                </div>
                <div class="ems-dashboard-card__body d-grid gap-3">
                    <div class="ems-insight-block">
                        <div class="label">Name</div>
                        <div class="value">{{ $user->name }}</div>
                        <div class="meta">Used across dashboard and check-in screens.</div>
                    </div>

                    <div class="ems-insight-block">
                        <div class="label">Email</div>
                        <div class="value">{{ $user->email }}</div>
                        <div class="meta">This is your login address.</div>
                    </div>

                    <div class="ems-insight-block">
                        <div class="label">Role</div>
                        <div class="value">{{ ucfirst($user->user_type ?? 'organizer') }}</div>
                        <div class="meta">Controls what data and routes are available.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="d-grid gap-4">
                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Edit profile</h2>
                            <p>Update your name and email address.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body">
                        @if ($errors->profile->any())
                            <div class="alert alert-danger border-0 rounded-4">
                                {{ $errors->profile->first() }}
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST" class="row g-3">
                            @csrf
                            <input type="hidden" name="type" value="profile">

                            <div class="col-md-6">
                                <label for="name" class="form-label">Full name</label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-lg @error('name', 'profile') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name', 'profile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" id="email"
                                    class="form-control form-control-lg @error('email', 'profile') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email', 'profile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Save profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="ems-dashboard-card" id="password">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Change password</h2>
                            <p>Use a strong password to secure the workspace.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body">
                        @if ($errors->change_password->any())
                            <div class="alert alert-danger border-0 rounded-4">
                                {{ $errors->change_password->first() }}
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST" class="row g-3">
                            @csrf
                            <input type="hidden" name="type" value="change_password">

                            <div class="col-md-6">
                                <label for="password" class="form-label">New password</label>
                                <input type="password" name="password" id="password"
                                    class="form-control form-control-lg @error('password', 'change_password') is-invalid @enderror"
                                    required>
                                @error('password', 'change_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control form-control-lg" required>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-lock me-2"></i>
                                    Update password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
