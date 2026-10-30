@extends('layouts.layoutMaster')

@section('title', 'Profile & Security')

@section('content')
    @php
        $user = $user ?? auth()->user();
        $roleLabel = str($user->user_type ?? 'organizer')->replace('_', ' ')->title();
        $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->join('');
    @endphp

    <div class="ep-profile-page">
        <x-ui.page-header eyebrow="Account settings" title="Profile & security"
            description="Keep your account identity accurate and your workspace access protected.">
            <x-slot:actions>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-2" aria-hidden="true"></i>Back to dashboard
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="ep-profile-layout">
            <aside class="ep-profile-identity" aria-label="Account summary">
                <div class="ep-profile-avatar" aria-hidden="true">{{ $initials ?: 'U' }}</div>
                <div class="ep-profile-identity__copy">
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="ep-profile-role"><i class="ti ti-shield-check" aria-hidden="true"></i><span>{{ $roleLabel }}</span></div>

                <dl class="ep-profile-facts">
                    <div><dt>Account status</dt><dd><span class="ep-profile-status"><span aria-hidden="true"></span>{{ $user->status ? 'Active' : 'Inactive' }}</span></dd></div>
                    <div><dt>Member since</dt><dd>{{ $user->created_at?->format('d M Y') ?? 'Not available' }}</dd></div>
                    <div><dt>Workspace access</dt><dd>{{ $roleLabel }}</dd></div>
                </dl>

                <nav class="ep-profile-nav" aria-label="Profile sections">
                    <a href="#personal-details"><i class="ti ti-user" aria-hidden="true"></i><span>Personal details</span></a>
                    <a href="#password-security"><i class="ti ti-lock" aria-hidden="true"></i><span>Password security</span></a>
                </nav>

                <div class="ep-profile-note">
                    <i class="ti ti-info-circle" aria-hidden="true"></i>
                    <p>Your role and account status are managed by the platform administrator.</p>
                </div>
            </aside>

            <div class="ep-profile-workspace">
                <section class="ep-profile-section" id="personal-details" aria-labelledby="personal-details-title">
                    <header class="ep-profile-section__head">
                        <div class="ep-profile-section__icon"><i class="ti ti-id" aria-hidden="true"></i></div>
                        <div><h2 id="personal-details-title">Personal details</h2><p>Used to identify you throughout the management workspace.</p></div>
                    </header>

                    @if ($errors->profile->any())
                        <div class="ep-form-errors" role="alert" tabindex="-1" data-error-summary>
                            <h3>Check your profile details</h3>
                            <ul>
                                @foreach ($errors->profile->messages() as $field => $messages)
                                    @foreach ($messages as $message)<li><a href="#{{ $field }}">{{ $message }}</a></li>@endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" class="ep-profile-form">
                        @csrf
                        <input type="hidden" name="type" value="profile">
                        <div class="ep-profile-fields">
                            <div>
                                <label for="name" class="form-label">Full name</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name', 'profile') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" autocomplete="name" maxlength="20" required
                                    @error('name', 'profile') aria-invalid="true" aria-describedby="name-error" @enderror>
                                <div class="form-text">Between 3 and 20 characters.</div>
                                @error('name', 'profile')<div class="invalid-feedback" id="name-error">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email', 'profile') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" autocomplete="email" required
                                    @error('email', 'profile') aria-invalid="true" aria-describedby="email-error" @enderror>
                                <div class="form-text">Used to sign in and receive account notices.</div>
                                @error('email', 'profile')<div class="invalid-feedback" id="email-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="ep-profile-actions"><p>Changes take effect the next time your account details are displayed.</p><button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-2" aria-hidden="true"></i>Save profile</button></div>
                    </form>
                </section>

                <section class="ep-profile-section" id="password-security" aria-labelledby="password-security-title">
                    <header class="ep-profile-section__head">
                        <div class="ep-profile-section__icon"><i class="ti ti-lock" aria-hidden="true"></i></div>
                        <div><h2 id="password-security-title">Password security</h2><p>Choose a unique password that is difficult to guess.</p></div>
                    </header>

                    @if ($errors->change_password->any())
                        <div class="ep-form-errors" role="alert" tabindex="-1" data-error-summary>
                            <h3>Check your new password</h3>
                            <ul>
                                @foreach ($errors->change_password->messages() as $field => $messages)
                                    @foreach ($messages as $message)<li><a href="#{{ $field }}">{{ $message }}</a></li>@endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" class="ep-profile-form">
                        @csrf
                        <input type="hidden" name="type" value="change_password">
                        <div class="ep-profile-fields">
                            <div>
                                <label for="password" class="form-label">New password</label>
                                <div class="ep-password-control">
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password', 'change_password') is-invalid @enderror"
                                        autocomplete="new-password" minlength="8" maxlength="64" required
                                        aria-describedby="password-requirements @error('password', 'change_password') password-error @enderror"
                                        @error('password', 'change_password') aria-invalid="true" @enderror>
                                    <button type="button" data-password-toggle="password" aria-label="Show new password" aria-pressed="false"><i class="ti ti-eye" aria-hidden="true"></i></button>
                                </div>
                                @error('password', 'change_password')<div class="invalid-feedback d-block" id="password-error">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="form-label">Confirm new password</label>
                                <div class="ep-password-control">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" minlength="8" maxlength="64" required>
                                    <button type="button" data-password-toggle="password_confirmation" aria-label="Show password confirmation" aria-pressed="false"><i class="ti ti-eye" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="ep-password-requirements" id="password-requirements">
                            <strong>Password requirements</strong>
                            <ul><li>8–64 characters</li><li>At least one uppercase letter</li><li>At least one lowercase letter</li><li>At least one special character</li></ul>
                        </div>
                        <div class="ep-profile-actions"><p>After updating, use the new password for your next sign-in.</p><button type="submit" class="btn btn-primary"><i class="ti ti-lock-check me-2" aria-hidden="true"></i>Update password</button></div>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const errorSummary = document.querySelector('[data-error-summary]');
    if (errorSummary) errorSummary.focus();

    document.querySelectorAll('[data-password-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.setAttribute('aria-pressed', showing ? 'false' : 'true');
            button.setAttribute('aria-label', `${showing ? 'Show' : 'Hide'} ${input.id === 'password' ? 'new password' : 'password confirmation'}`);
            button.querySelector('i')?.classList.toggle('ti-eye', showing);
            button.querySelector('i')?.classList.toggle('ti-eye-off', !showing);
        });
    });
});
</script>
@endsection
