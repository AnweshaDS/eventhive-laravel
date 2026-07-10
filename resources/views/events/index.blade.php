@extends('layouts.app')

@section('title', 'Browse Events — EventHive')

@section('content')

<div style="background:var(--dark-2); padding:2rem;">
    <div style="max-width:1200px; margin:0 auto;">
        <h1 style="font-size:1.8rem; margin-bottom:0.3rem;">
            <i class="fa-solid fa-calendar-days" style="color:var(--primary)"></i> Browse Events
        </h1>
        <p style="color:var(--text-muted);">
            {{ $events->count() }} event{{ $events->count() !== 1 ? 's' : '' }} found
            @if($search) for "<strong>{{ $search }}</strong>" @endif
        </p>
    </div>
</div>

<div style="max-width:1200px; margin:0 auto; padding:2rem;">

    <!-- FILTERS -->
    <form method="GET" action="{{ route('events.index') }}"
          style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem; margin-bottom:2rem;">
        <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:0.75rem; align-items:end;">
            <div class="form-group" style="margin:0;">
                <label><i class="fa-solid fa-magnifying-glass"></i> Search</label>
                <input type="text" name="search" placeholder="Event name, venue, city..." value="{{ $search }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label><i class="fa-solid fa-tag"></i> Category</label>
                <select name="category">
                    <option value="">All Categories</option>
                    @foreach(array_keys($icons) as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label><i class="fa-solid fa-location-dot"></i> City</label>
                <select name="city">
                    <option value="">All Cities</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ $city === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label><i class="fa-solid fa-arrow-up-wide-short"></i> Sort By</label>
                <select name="sort">
                    <option value="date"    {{ $sort === 'date'    ? 'selected' : '' }}>Date</option>
                    <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Popular</option>
                </select>
            </div>
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <a href="{{ route('events.index') }}" class="btn btn-outline">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        </div>
    </form>

    <!-- CATEGORY PILLS -->
    <div class="categories">
        <a href="{{ route('events.index', ['sort' => $sort]) }}"
           class="category-pill {{ !$category ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i> All
        </a>
        @foreach($icons as $cat => $icon)
        <a href="{{ route('events.index', ['category' => $cat, 'sort' => $sort]) }}"
           class="category-pill {{ $category === $cat ? 'active' : '' }}">
            <i class="fa-solid {{ $icon }}"></i> {{ $cat }}
        </a>
        @endforeach
    </div>

    <!-- EVENTS GRID -->
    @if($events->isEmpty())
        <div style="text-align:center; padding:4rem; color:var(--text-muted);">
            <i class="fa-solid fa-calendar-xmark" style="font-size:3rem; display:block; margin-bottom:1rem; color:var(--border)"></i>
            <h3 style="margin-bottom:0.5rem;">No events found</h3>
            <p>Try adjusting your filters or check back later.</p>
            <a href="{{ route('events.index') }}" class="btn btn-outline" style="margin-top:1rem;">Clear Filters</a>
        </div>
    @else
    <div class="events-grid">
        @foreach($events as $event)
        @php $seats_left = $event->available_seats; @endphp
        <div class="event-card">
            <div class="card-img">
                @if($event->banner_image)
                    <img src="{{ asset('images/' . $event->banner_image) }}"
                         alt="{{ $event->title }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <i class="fa-solid {{ $icons[$event->category] ?? 'fa-calendar-days' }}"></i>
                @endif
            </div>
            <div class="card-body">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.4rem;">
                    <div class="card-category">
                        <i class="fa-solid {{ $icons[$event->category] ?? 'fa-calendar-days' }}"></i>
                        {{ $event->category }}
                    </div>
                    @if($seats_left <= 0)
                        <span class="badge badge-danger">Sold Out</span>
                    @elseif($seats_left <= 10)
                        <span class="badge badge-warning">Only {{ $seats_left }} left</span>
                    @else
                        <span class="badge badge-success">{{ $seats_left }} seats</span>
                    @endif
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
                    @if($seats_left <= 0)
                        <button class="btn btn-sm" disabled style="opacity:0.5;">Sold Out</button>
                    @else
                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-ticket"></i> Book Now
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection