@extends('layouts.app')
@section('title', 'Team Login')

@section('content')
<main class="ems-login-page">
    <div class="container">
        <div class="ems-login-shell">
            <section class="ems-login-intro">
                <a href="{{ url('/') }}" class="ems-login-back"><i class="ti ti-arrow-left"></i> Back to events</a>
                <div>
                    <span class="ems-public-eyebrow"><i class="ti ti-lock"></i> Authorized team access</span>
                    <h1>Run every event<br>with clarity.</h1>
                    <p>One focused workspace for organizers, platform administrators, and venue security teams.</p>
                </div>
                <div class="ems-login-role-grid">
                    <div><i class="ti ti-settings-cog"></i><span><strong>Superadmin</strong><small>Platform and organizer control</small></span></div>
                    <div><i class="ti ti-calendar-event"></i><span><strong>Organizer</strong><small>Events, tickets, and attendees</small></span></div>
                    <div><i class="ti ti-shield-lock"></i><span><strong>Security officer</strong><small>Dedicated venue access</small></span></div>
                </div>
            </section>

            <section class="ems-login-form-panel">
                <div class="ems-login-form-wrap">
                    <div class="mb-4"><span class="ems-login-lock"><i class="ti ti-user-check"></i></span><h2>Welcome back</h2><p>Sign in with the credentials provided by your administrator.</p></div>
                    @if($errors->any())
                        <div class="alert alert-danger border-0"><strong>Sign-in failed.</strong> {{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('login') }}" class="ems-login-form d-grid gap-3" data-login-form>
                        @csrf
                        <div>
                            <label for="email" class="form-label">Email address</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="you@company.com" autocomplete="email" autofocus required>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label">Password</label>
                                @if(\Illuminate\Support\Facades\Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                                @endif
                            </div>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                <input id="password" type="password" class="form-control" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password aria-label="Show password"><i class="ti ti-eye"></i></button>
                            </div>
                        </div>
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="form-check-label">Keep me signed in on this device</span>
                        </label>
                        <button class="btn btn-dark btn-lg ems-login-submit" type="submit" data-login-submit>
                            <span data-login-submit-label>Sign in</span>
                            <i class="ti ti-arrow-right ms-2" data-login-submit-arrow></i>
                            <span class="spinner-border spinner-border-sm d-none ms-2" aria-hidden="true" data-login-submit-spinner></span>
                        </button>
                    </form>
                    <div class="ems-login-help"><i class="ti ti-info-circle"></i><span>Accounts are created by a superadmin or organizer. Contact your administrator if you need access.</span></div>
                </div>
            </section>
        </div>
    </div>
</main>
@endsection

@section('page-script')
<script>
document.querySelector('[data-toggle-password]')?.addEventListener('click', event => {
    const input = document.getElementById('password');
    const icon = event.currentTarget.querySelector('i');
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
});

document.querySelector('[data-login-form]')?.addEventListener('submit', event => {
    const button = event.currentTarget.querySelector('[data-login-submit]');
    if (!button || button.disabled) {
        return;
    }

    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.querySelector('[data-login-submit-label]').textContent = 'Signing in...';
    button.querySelector('[data-login-submit-arrow]').classList.add('d-none');
    button.querySelector('[data-login-submit-spinner]').classList.remove('d-none');
});
</script>
@endsection
