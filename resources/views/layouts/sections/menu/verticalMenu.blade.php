@php
    use Illuminate\Support\Facades\Route;
    $configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    @if (!isset($navbarFull))
        <div class="app-brand">
            <a href="{{ route('dashboard') }}" class="app-brand-link">
                <img src="{{ asset('assets/img/hexa-logo.png') }}" alt="EventOps" class="ems-sidebar-logo">
            </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none"><i class="ti ti-x"></i></a>
        </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach ($menuData[0]->menu as $menu)
            {{-- Check if user has access to this menu item and if it matches the environment --}}
            @if (isset($menu->roles) && in_array(auth()->user()->user_type, $menu->roles))

                @if (isset($menu->menuHeader))
                    <li class="menu-header small">
                        <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                    </li>
                @else
                    @php
                        $activeClass = null;
                        $currentRouteName = Route::currentRouteName();

                        if ($currentRouteName === $menu->slug ||
                            (!isset($menu->submenu) && $currentRouteName && str_starts_with($currentRouteName, $menu->slug . '.'))) {
                            $activeClass = 'active';
                        } elseif (isset($menu->submenu)) {
                            if (gettype($menu->slug) === 'array') {
                                foreach ($menu->slug as $slug) {
                                    if (
                                        str_contains($currentRouteName, $slug) &&
                                        strpos($currentRouteName, $slug) === 0
                                    ) {
                                        $activeClass = 'active open';
                                    }
                                }
                            } else {
                                if (
                                    str_contains($currentRouteName, $menu->slug) &&
                                    strpos($currentRouteName, $menu->slug) === 0
                                ) {
                                    $activeClass = 'active open';
                                }
                            }
                        }
                    @endphp

                    <li class="menu-item {{ $activeClass }}">
                        <a href="{{ isset($menu->url) ? (Route::has($menu->url) ? route($menu->url) : url($menu->url)) : 'javascript:void(0);' }}"
                            class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                            @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>
                            @isset($menu->icon)
                                <i class="{{ $menu->icon }}"></i>
                            @endisset
                            <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                            @isset($menu->badge)
                                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                            @endisset
                        </a>

                        {{-- submenu --}}
                        @isset($menu->submenu)
                            @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
                        @endisset
                    </li>
                @endif
            @endif
        @endforeach
    </ul>
    <div class="ems-sidebar-account">
        <div class="ems-sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="min-w-0">
            <div class="fw-semibold text-truncate">{{ auth()->user()->name }}</div>
            <div class="small text-muted text-truncate">{{ auth()->user()->isSuperadmin() ? 'Superadmin' : (auth()->user()->isSecurityOfficer() ? 'Security officer' : 'Organizer') }}</div>
        </div>
        <a href="{{ route('profile.show') }}" class="ms-auto" aria-label="Account settings"><i class="ti ti-settings"></i></a>
    </div>
</aside>
