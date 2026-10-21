@extends('layouts.app')
@section('title', 'Discover Events')

@section('content')
<section class="ems-public-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="ems-public-eyebrow"><i class="ti ti-sparkles"></i> Events worth showing up for</span>
                <h1>Find your next<br><span>great experience.</span></h1>
                <p>Explore upcoming events, compare ticket options, and reserve your place through a fast, straightforward booking experience.</p>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <a href="#events" class="btn btn-dark btn-lg px-4">Browse upcoming events <i class="ti ti-arrow-down ms-2"></i></a>
                    <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg px-4">Organizer login</a>
                </div>
                <div class="ems-public-trust">
                    <span><i class="ti ti-shield-check"></i> Secure registration</span>
                    <span><i class="ti ti-ticket"></i> Clear ticket options</span>
                    <span><i class="ti ti-device-mobile"></i> Mobile friendly</span>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="ems-public-featured">
                    @if($events->first()?->banner_image)
                        <img src="{{ asset('storage/'.$events->first()->banner_image) }}" alt="{{ $events->first()->name }}">
                    @else
                        <div class="ems-public-featured__placeholder"><i class="ti ti-calendar-event"></i></div>
                    @endif
                    <div class="ems-public-featured__content">
                        <span>Next up</span>
                        <h2>{{ $events->first()?->name ?? 'New events coming soon' }}</h2>
                        @if($events->first())
                            <p><i class="ti ti-calendar me-2"></i>{{ $events->first()->event_start_date?->format('D, d M Y') }}</p>
                            <p><i class="ti ti-map-pin me-2"></i>{{ $events->first()->location }}</p>
                            <a href="{{ route('events.registration', $events->first()->event_code) }}">View event <i class="ti ti-arrow-up-right"></i></a>
                        @else
                            <p>Our next public event will appear here as soon as booking opens.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ems-public-events" id="events">
    <div class="container">
        <div class="ems-public-section-head">
            <div><span class="ems-public-eyebrow">Upcoming calendar</span><h2>Choose an event</h2><p>Everything you need to decide, presented clearly.</p></div>
            <span class="ems-public-count">{{ $events->count() }} {{ \Illuminate\Support\Str::plural('event', $events->count()) }}</span>
        </div>
        <div class="row g-4">
            @forelse($events as $event)
                @php
                    $soldOut = $event->attendees_count >= $event->max_attendees;
                    $fromPrice = $event->ticketing_type === 'ticketed' ? $event->ticketTypes->min('price') : 0;
                    $remaining = max($event->max_attendees - $event->attendees_count, 0);
                @endphp
                <div class="col-md-6 col-xl-4">
                    <article class="ems-public-event-card">
                        <div class="ems-public-event-card__media">
                            @if($event->banner_image)<img src="{{ asset('storage/'.$event->banner_image) }}" alt="{{ $event->name }}">@else<div class="ems-public-event-card__placeholder"><i class="ti ti-calendar-event"></i></div>@endif
                            <span class="ems-public-date"><strong>{{ $event->event_start_date?->format('d') }}</strong>{{ $event->event_start_date?->format('M') }}</span>
                            <span class="ems-public-price">{{ $soldOut ? 'Sold out' : ($fromPrice > 0 ? 'From '.number_format($fromPrice, 2) : 'Free') }}</span>
                        </div>
                        <div class="ems-public-event-card__body">
                            <div class="ems-public-event-meta"><span><i class="ti ti-map-pin"></i>{{ $event->location }}</span></div>
                            <h3>{{ $event->name }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($event->description ?: 'View event information and reserve your place online.', 110) }}</p>
                            <div class="ems-public-capacity"><span>{{ $soldOut ? 'No places remaining' : $remaining.' places remaining' }}</span><span>{{ $event->attendees_count }}/{{ $event->max_attendees }}</span></div>
                            <div class="progress"><div class="progress-bar" style="width: {{ min(($event->attendees_count / max($event->max_attendees, 1))*100, 100) }}%"></div></div>
                            <a href="{{ route('events.registration', $event->event_code) }}" class="btn btn-dark w-100 {{ $soldOut ? 'disabled' : '' }}">{{ $soldOut ? 'Fully booked' : 'View details and book' }}</a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="ems-public-empty"><i class="ti ti-calendar-off"></i><h3>No events are open right now</h3><p>New public events will appear here when registration opens.</p></div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="ems-public-how">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 620px"><span class="ems-public-eyebrow">Simple by design</span><h2>Book your place in minutes</h2><p>Clear information and no unnecessary account creation.</p></div>
        <div class="row g-4">
            @foreach([['01','Choose','Browse upcoming events and open the one that interests you.'],['02','Select','Review admission options and select the right ticket.'],['03','Register','Enter your details and receive your booking confirmation.']] as [$number,$title,$copy])
                <div class="col-md-4"><div class="ems-public-step"><span>{{ $number }}</span><h3>{{ $title }}</h3><p>{{ $copy }}</p></div></div>
            @endforeach
        </div>
    </div>
</section>
@endsection
