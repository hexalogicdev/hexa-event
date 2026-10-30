@props(['event'])

@php
    $items = [
        ['route' => 'events.show', 'label' => 'Overview', 'icon' => 'layout-dashboard'],
        ['route' => 'events.edit', 'label' => 'Event details', 'icon' => 'edit'],
        ['route' => 'events.fields', 'label' => 'Registration form', 'icon' => 'forms'],
        ['route' => 'events.attendees', 'label' => 'Attendees', 'icon' => 'users'],
        ['route' => 'events.security-officers', 'label' => 'Security team', 'icon' => 'shield-lock'],
        ['route' => 'events.report', 'label' => 'Reports', 'icon' => 'chart-bar'],
    ];
@endphp

<nav class="ep-event-nav" aria-label="{{ $event->name }} management">
    <div class="ep-event-nav__context">
        <span class="ep-event-nav__label">Managing event</span>
        <strong>{{ $event->name }}</strong>
    </div>
    <div class="ep-event-nav__links">
        @foreach ($items as $item)
            <a href="{{ route($item['route'], $event) }}" @class(['is-active' => request()->routeIs($item['route'])])
                @if(request()->routeIs($item['route'])) aria-current="page" @endif>
                <i class="ti ti-{{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
