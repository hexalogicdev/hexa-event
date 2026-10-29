@php
    $homeUrl = url('/');
    $isHome = request()->is('/');
@endphp

<header class="eventify-site-header" data-public-header>
    <div class="container eventify-header-inner">
        <a class="eventify-header-brand" href="{{ $homeUrl }}" aria-label="{{ $appName }} home">
            <span class="eventify-brand-mark" aria-hidden="true"><i class="ti ti-calendar-event"></i></span>
            <span>{{ $appName }}</span>
        </a>

        <nav class="eventify-desktop-nav" aria-label="Primary navigation">
            <a href="{{ $homeUrl }}#events" @class(['is-active' => $isHome && ! request()->filled('category')])>Discover Events</a>
            <a href="{{ $homeUrl }}#categories">Categories</a>
            <a href="{{ $homeUrl }}#why-us">How It Works</a>
            <a href="#eventify-footer">Contact</a>
        </nav>

        <div class="eventify-header-actions">
            <a class="eventify-header-search" href="{{ $homeUrl }}#event-search" aria-label="Search events">
                <i class="ti ti-search" aria-hidden="true"></i><span>Search</span>
            </a>
            @auth
                <a class="btn btn-eventify-outline eventify-account-action" href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a class="btn btn-eventify-outline eventify-account-action" href="{{ route('login') }}">Organizer Login</a>
            @endauth
            <button class="eventify-menu-toggle" type="button" aria-expanded="false" aria-controls="eventify-mobile-menu" data-menu-toggle>
                <span class="visually-hidden">Open navigation menu</span>
                <i class="ti ti-menu-2" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="eventify-mobile-backdrop" data-menu-backdrop hidden></div>
    <aside class="eventify-mobile-menu" id="eventify-mobile-menu" aria-label="Mobile navigation" aria-hidden="true" data-mobile-menu>
        <div class="eventify-mobile-menu__head">
            <a class="eventify-header-brand" href="{{ $homeUrl }}" data-menu-link>
                <span class="eventify-brand-mark" aria-hidden="true"><i class="ti ti-calendar-event"></i></span><span>{{ $appName }}</span>
            </a>
            <button type="button" class="eventify-menu-close" aria-label="Close navigation menu" data-menu-close><i class="ti ti-x" aria-hidden="true"></i></button>
        </div>
        <nav class="eventify-mobile-nav" aria-label="Mobile primary navigation">
            <a href="{{ $homeUrl }}#events" data-menu-link><i class="ti ti-calendar-search" aria-hidden="true"></i>Discover Events</a>
            <a href="{{ $homeUrl }}#categories" data-menu-link><i class="ti ti-category" aria-hidden="true"></i>Categories</a>
            <a href="{{ $homeUrl }}#why-us" data-menu-link><i class="ti ti-route" aria-hidden="true"></i>How It Works</a>
            <a href="#eventify-footer" data-menu-link><i class="ti ti-message-circle" aria-hidden="true"></i>Contact</a>
        </nav>
        <div class="eventify-mobile-menu__actions">
            <a class="eventify-mobile-search" href="{{ $homeUrl }}#event-search" data-menu-link><i class="ti ti-search" aria-hidden="true"></i>Search events</a>
            @auth
                <a class="btn btn-eventify w-100" href="{{ route('dashboard') }}" data-menu-link>Open dashboard</a>
            @else
                <a class="btn btn-eventify w-100" href="{{ route('login') }}" data-menu-link>Organizer Login</a>
            @endauth
        </div>
    </aside>
</header>

@once
    @push('scripts')
        <script>
            (() => {
                const header = document.querySelector('[data-public-header]');
                if (!header) return;
                const toggle = header.querySelector('[data-menu-toggle]');
                const menu = header.querySelector('[data-mobile-menu]');
                const closeButton = header.querySelector('[data-menu-close]');
                const backdrop = header.querySelector('[data-menu-backdrop]');
                let previousFocus = null;
                const focusableSelector = 'a[href], button:not([disabled])';

                const closeMenu = () => {
                    if (menu.getAttribute('aria-hidden') === 'true') return;
                    menu.setAttribute('aria-hidden', 'true');
                    toggle.setAttribute('aria-expanded', 'false');
                    backdrop.hidden = true;
                    document.body.classList.remove('eventify-menu-open');
                    previousFocus?.focus();
                };
                const openMenu = () => {
                    previousFocus = document.activeElement;
                    menu.setAttribute('aria-hidden', 'false');
                    toggle.setAttribute('aria-expanded', 'true');
                    backdrop.hidden = false;
                    document.body.classList.add('eventify-menu-open');
                    closeButton.focus();
                };

                toggle.addEventListener('click', () => toggle.getAttribute('aria-expanded') === 'true' ? closeMenu() : openMenu());
                closeButton.addEventListener('click', closeMenu);
                backdrop.addEventListener('click', closeMenu);
                header.querySelectorAll('[data-menu-link]').forEach(link => link.addEventListener('click', closeMenu));
                document.addEventListener('keydown', event => {
                    if (menu.getAttribute('aria-hidden') === 'true') return;
                    if (event.key === 'Escape') { event.preventDefault(); closeMenu(); return; }
                    if (event.key !== 'Tab') return;
                    const focusable = [...menu.querySelectorAll(focusableSelector)];
                    const first = focusable[0];
                    const last = focusable[focusable.length - 1];
                    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
                });
                window.matchMedia('(min-width: 992px)').addEventListener('change', event => { if (event.matches) closeMenu(); });
            })();
        </script>
    @endpush
@endonce
