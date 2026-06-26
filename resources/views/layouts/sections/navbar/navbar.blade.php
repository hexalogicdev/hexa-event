@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    $isDashboard = request()->routeIs('dashboard');
    $isEvents = request()->routeIs('events.*');
    $user = Auth::user();
    $userName = $user?->name ?? 'Account';
    $userType = $user?->user_type ?? '';
    $containerNav = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
@endphp

<nav class="layout-navbar ems-app-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="{{ $containerNav }}">
        <div class="ems-app-navbar__inner">
            <a class="navbar-brand d-flex align-items-center gap-2 m-0 ems-app-navbar__brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/img/hexa-logo.png') }}" alt="HexaLogicDev" class="ems-brand-logo">
            </a>

            <div class="ems-app-navbar__nav">
                <a href="{{ route('dashboard') }}" class="ems-app-navbar__link {{ $isDashboard ? 'is-active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('events.index') }}" class="ems-app-navbar__link {{ $isEvents ? 'is-active' : '' }}">
                    <i class="ti ti-calendar-event"></i>
                    <span>Events</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="{{ route('logout') }}"
                    class="btn btn-outline-light btn-sm d-inline-flex align-items-center ems-logout-action"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="ti ti-logout me-2"></i>
                    Logout
                </a>

                <div class="dropdown navbar-dropdown dropdown-user">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="{{ route('profile.show') }}" aria-label="Open profile">
                    <div class="avatar avatar-online">
                        <img src="{{ asset('assets/img/avatars/dummy.png') }}" alt class="rounded-circle">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item mt-0" href="{{ route('profile.show') }}">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('assets/img/avatars/dummy.png') }}" alt class="rounded-circle">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ ucwords(strtolower($userName)) }}</h6>
                                    @if ($userType)
                                        <small class="text-muted">{{ ucwords(strtolower($userType)) }}</small>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="ti ti-user me-3 ti-md"></i><span class="align-middle">Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}#password">
                            <i class="ti ti-lock me-3 ti-md"></i><span class="align-middle">Change password</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    <li>
                        <div class="d-grid px-2 pt-2 pb-1">
                            <a class="btn btn-sm btn-danger d-flex ems-logout-action" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <small class="align-middle">Logout</small>
                                <i class="ti ti-logout ms-2 ti-14px"></i>
                            </a>
                        </div>
                    </li>
                </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<form method="POST" id="logout-form" action="{{ route('logout') }}" class="d-none">
    @csrf
</form>
