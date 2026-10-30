@php
    $user = auth()->user();
    $pageTitle = match (true) {
        request()->routeIs('dashboard') => 'Overview',
        request()->routeIs('events.create') => 'Create event',
        request()->routeIs('events.show') => 'Event overview',
        request()->routeIs('events.edit') => 'Edit event',
        request()->routeIs('events.fields') => 'Registration form',
        request()->routeIs('events.attendees') => 'Event attendees',
        request()->routeIs('events.security-officers') => 'Event security team',
        request()->routeIs('events.report') => 'Event reports',
        request()->routeIs('events.*') => 'Event management',
        request()->routeIs('organizations.show') => 'Organization workspace',
        request()->routeIs('organizations.*') => 'Organizations',
        request()->routeIs('event-allowance-requests.*') => 'Allowance requests',
        request()->routeIs('User.*') => 'Organizer management',
        request()->routeIs('security-officers.*') => 'Security team',
        request()->routeIs('attendee.qr*') => 'QR check-in',
        request()->routeIs('profile.*') => 'Account settings',
        default => 'Event operations',
    };
    $containerNav = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
@endphp

<nav class="layout-navbar ems-app-navbar navbar navbar-expand-xl" id="layout-navbar">
    <div class="{{ $containerNav }}">
        <div class="ems-app-navbar__inner w-100">
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link d-xl-none ems-navbar-toggle" aria-label="Open navigation"><i class="ti ti-menu-2 ti-md"></i></a>
            <div class="ems-navbar-context">
                <div class="ems-app-navbar__eyebrow">{{ $user->isSuperadmin() ? 'Platform administration' : ($user->isSecurityOfficer() ? 'Venue access control' : 'Organizer workspace') }}</div>
                <h1 class="ems-app-navbar__title">{{ $pageTitle }}</h1>
            </div>
            <div class="ems-navbar-actions">
                @unless($user->isSecurityOfficer())
                    <a href="{{ route('events.create') }}" class="btn btn-primary d-none d-sm-inline-flex"><i class="ti ti-plus me-2"></i>New event</a>
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary d-none d-md-inline-flex"><i class="ti ti-external-link me-2"></i>View site</a>
                @endunless
                <div class="dropdown">
                    <button class="ems-account-button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open account menu">
                        <span class="ems-sidebar-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <span class="d-none d-lg-block text-start"><strong>{{ $user->name }}</strong><small>{{ $user->isSuperadmin() ? 'Superadmin' : ($user->isSecurityOfficer() ? 'Security officer' : 'Organizer') }}</small></span>
                        <i class="ti ti-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        @unless($user->isSecurityOfficer())
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="ti ti-user me-2"></i>Profile and security</a></li>
                            <li><hr class="dropdown-divider"></li>
                        @endunless
                        <li><a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="ti ti-logout me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<form method="POST" id="logout-form" action="{{ route('logout') }}" class="d-none">@csrf</form>
