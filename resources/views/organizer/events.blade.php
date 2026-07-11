@extends('layouts.app')
@section('title', 'My Events — EventHive')
@section('content')

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="logo">Event<span style="color:var(--secondary)">Hive</span></div>
        <nav class="sidebar-nav">
            <a href="{{ route('organizer.dashboard') }}">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="{{ route('organizer.create') }}">
                <i class="fa-solid fa-calendar-plus"></i> Create Event
            </a>
            <a href="{{ route('organizer.events') }}" class="active">
                <i class="fa-solid fa-list"></i> My Events
            </a>
            <a href="{{ route('organizer.attendees') }}">
                <i class="fa-solid fa-users"></i> Attendees
            </a>
            <a href="{{ route('events.index') }}">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Browse Events
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:auto;">
                @csrf
                <button type="submit" style="background:none; border:none; cursor:pointer; width:100%; text-align:left; padding:0.75rem 1rem; color:var(--danger); font-size:0.92rem; display:flex; align-items:center; gap:0.75rem;">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <div class="dashboard-main">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <h1 style="font-size:1.6rem;">My Events</h1>
                <p style="color:var(--text-muted);">{{ $events->count() }} event{{ $events->count() !== 1 ? 's' : '' }} total</p>
            </div>
            <a href="{{ route('organizer.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Create New Event
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif

        @if($events->isEmpty())
            <div style="text-align:center; padding:4rem; background:var(--card-bg); border-radius:var(--radius); border:1px solid var(--border); color:var(--text-muted);">
                <i class="fa-solid fa-calendar-xmark" style="font-size:3rem; display:block; margin-bottom:1rem;"></i>
                <h3 style="margin-bottom:0.5rem;">No events yet</h3>
                <a href="{{ route('organizer.create') }}" class="btn btn-primary" style="margin-top:1rem;">Create your first event</a>
            </div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Bookings</th>
                        <th>Revenue</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $e)
                    <tr>
                        <td>
                            <strong>{{ $e->title }}</strong>
                            <div style="font-size:0.8rem; color:var(--text-muted);">
                                <i class="fa-solid fa-location-dot"></i> {{ $e->city }}
                            </div>
                        </td>
                        <td style="color:var(--text-muted); font-size:0.88rem;">
                            {{ $e->event_date->format('M j, Y') }}
                        </td>
                        <td>
                            @php $badge = match($e->status) { 'published'=>'badge-success','draft'=>'badge-warning','cancelled'=>'badge-danger', default=>'badge-primary' }; @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($e->status) }}</span>
                        </td>
                        <td>
                            <span style="color:var(--primary); font-weight:600;">{{ $e->ticketTypes->sum('booked_seats') }}</span>
                            <span style="color:var(--text-muted); font-size:0.85rem;"> / {{ $e->ticketTypes->sum('total_seats') }}</span>
                        </td>
                        <td style="color:var(--success); font-weight:600;">
                            ৳{{ number_format($e->ticketTypes->sum(fn($t) => $t->booked_seats * $t->price), 0) }}
                        </td>
                        <td>
                            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <a href="{{ route('events.show', $e->id) }}" class="btn btn-sm" style="background:var(--dark); border:1px solid var(--border);" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('organizer.edit', $e->id) }}" class="btn btn-sm btn-outline" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="{{ route('organizer.attendees', ['event_id' => $e->id]) }}" class="btn btn-sm btn-primary" title="Attendees">
                                    <i class="fa-solid fa-users"></i>
                                </a>
                                <form method="POST" action="{{ route('organizer.destroy', $e->id) }}"
                                      onsubmit="return confirm('Delete this event?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection