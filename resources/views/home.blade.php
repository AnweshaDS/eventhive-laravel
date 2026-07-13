@extends('layouts.app')

@section('title', 'EventHive — Discover, Host & Attend Events Near You')

@section('content')

<!-- HERO -->
<section class="hero">
    <h1>Discover & Book <span>Amazing Events</span><br>Near You</h1>
    <p>Concerts, workshops, food fests, conferences - find and book tickets for any public event, all in one place.</p>
    <div class="hero-btns">
        <a href="{{ route('events.index') }}" class="btn btn-primary btn-lg">Explore Events</a>
        <a href="{{ route('register') }}" class="btn btn-outline btn-lg">Host an Event</a>
    </div>
    <div class="hero-stats">
        <div class="stat-item">
            <h3>{{ $stats['live_events'] }}</h3>
            <p>Live Events</p>
        </div>
        <div class="stat-item">
            <h3>{{ $stats['organizers'] }}</h3>
            <p>Organizers</p>
        </div>
        <div class="stat-item">
            <h3>{{ $stats['tickets_sold'] }}</h3>
            <p>Tickets Booked</p>
        </div>
    </div>
</section>

<!-- UPCOMING EVENTS -->
<div class="section">
    <h2 class="section-title">Upcoming <span>Events</span></h2>
    <p class="section-sub">Don't miss out — grab your tickets before they sell out</p>

    <div class="categories">
        <span class="category-pill active" onclick="filterCategory(this, '')">
            <i class="fa-solid fa-border-all"></i> All
        </span>
        @foreach(['Concert'=>'fa-music','Workshop'=>'fa-screwdriver-wrench','Conference'=>'fa-briefcase','Food'=>'fa-utensils','Sports'=>'fa-futbol','Tech'=>'fa-laptop-code'] as $cat => $icon)
        <span class="category-pill" onclick="filterCategory(this, '{{ $cat }}')">
            <i class="fa-solid {{ $icon }}"></i> {{ $cat }}
        </span>
        @endforeach
    </div>

    @php
    $icons = [
        'Concert'=>'fa-music','Workshop'=>'fa-screwdriver-wrench',
        'Conference'=>'fa-briefcase','Food'=>'fa-utensils',
        'Sports'=>'fa-futbol','Comedy'=>'fa-face-laugh',
        'Tech'=>'fa-laptop-code','Art'=>'fa-palette',
    ];
    @endphp

    @if($events->isEmpty())
        <div style="text-align:center; padding:3rem; color:var(--text-muted);">
            <i class="fa-solid fa-calendar-xmark" style="font-size:3rem; display:block; margin-bottom:1rem;"></i>
            <p>No events yet. Be the first to <a href="{{ route('register') }}" style="color:var(--primary)">host one!</a></p>
        </div>
    @else
    <div class="events-grid" id="events-grid">
        @foreach($events as $event)
        <div class="event-card" data-category="{{ $event->category }}">
            <div class="card-img">
                @if($event->banner_image)
                    <img src="{{ asset('images/' . $event->banner_image) }}"
                         alt="{{ $event->title }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <i class="fa-solid {{ $icons[$event->category] ?? 'fa-calendar-days' }}"></i>
                @endif
            </div>
            <div class="card-body">
                <div class="card-category">
                    <i class="fa-solid {{ $icons[$event->category] ?? 'fa-calendar-days' }}"></i>
                    {{ $event->category }}
                </div>
                <div class="card-title">{{ $event->title }}</div>
                <div class="card-meta">
                    <span><i class="fa-regular fa-calendar"></i> {{ $event->event_date->format('D, M j Y · g:i A') }}</span>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $event->venue }}, {{ $event->city }}</span>
                    <span><i class="fa-regular fa-user"></i> By {{ $event->organizer->name }}</span>
                </div>
                <div class="card-footer">
                    <span class="price-tag {{ $event->min_price == 0 ? 'free' : '' }}">
                        @if($event->min_price == 0)
                            <i class="fa-solid fa-gift"></i> Free
                        @else
                            <i class="fa-solid fa-tag"></i> ৳{{ number_format($event->min_price, 0) }}
                        @endif
                    </span>
                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-ticket"></i> Book Now
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div style="text-align:center; margin-top:2rem;">
        <a href="{{ route('events.index') }}" class="btn btn-outline">View All Events →</a>
    </div>
</div>

<!-- HOW IT WORKS -->
<div style="background:var(--dark-2); padding:3rem 2rem;">
    <div class="section" style="padding:0;">
        <h2 class="section-title" style="text-align:center">How <span>EventHive</span> Works</h2>
        <p class="section-sub" style="text-align:center">Three simple steps to your next great experience</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:2rem; margin-top:1rem;">
            @foreach([
                ['fa-magnifying-glass', 'Discover', 'Browse hundreds of public events by category, city, or date - all on one platform.'],
                ['fa-ticket', 'Book', 'Choose your ticket tier, confirm your booking, and receive a QR code instantly.'],
                ['fa-circle-check', 'Attend', 'Show your QR code at the door and enjoy the event - fast, paperless, and secure.'],
            ] as $step)
            <div style="text-align:center; padding:1.5rem; background:var(--card-bg); border-radius:var(--radius); border:1px solid var(--border);">
                <div style="font-size:2.5rem; margin-bottom:0.75rem; color:var(--primary);">
                    <i class="fa-solid {{ $step[0] }}"></i>
                </div>
                <h3 style="margin-bottom:0.5rem; font-size:1.1rem;">{{ $step[1] }}</h3>
                <p style="color:var(--text-muted); font-size:0.88rem;">{{ $step[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection