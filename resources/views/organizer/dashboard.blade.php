@extends('layouts.app')
@section('title', 'Organizer Dashboard — EventHive')
@section('content')

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="logo">Event<span style="color:var(--secondary)">Hive</span></div>
        <nav class="sidebar-nav">
            <a href="{{ route('organizer.dashboard') }}" class="active">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="{{ route('organizer.create') }}">
                <i class="fa-solid fa-calendar-plus"></i> Create Event
            </a>
            <a href="{{ route('organizer.events') }}">
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
        <div class="dash-header">
            <h1>Welcome back, {{ auth()->user()->name }} </h1>
            <p>Here's what's happening with your events</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-calendar-days"></i> Total Events</div>
                <div class="stat-value">{{ $stats['total_events'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-circle-dot"></i> Live Events</div>
                <div class="stat-value">{{ $stats['live_events'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-ticket"></i> Total Bookings</div>
                <div class="stat-value">{{ $stats['total_bookings'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-bangladeshi-taka-sign"></i> Revenue</div>
                <div class="stat-value">৳{{ number_format($stats['total_revenue'], 0) }}</div>
            </div>
        </div>

        <div style="margin-bottom:2rem;">
            <a href="{{ route('organizer.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Create New Event
            </a>
        </div>

        <h2 style="font-size:1.1rem; margin-bottom:1rem;">
            <i class="fa-solid fa-clock-rotate-left" style="color:var(--primary)"></i> Recent Events
        </h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Bookings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_events as $e)
                    <tr>
                        <td>
                            <strong>{{ $e->title }}</strong>
                            <div style="font-size:0.8rem; color:var(--text-muted);">{{ $e->city }}</div>
                        </td>
                        <td style="color:var(--text-muted); font-size:0.88rem;">
                            {{ $e->event_date->format('M j, Y') }}
                        </td>
                        <td>
                            @php $badge = match($e->status) { 'published'=>'badge-success','draft'=>'badge-warning','cancelled'=>'badge-danger', default=>'badge-primary' }; @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($e->status) }}</span>
                        </td>
                        <td>
                            <span style="color:var(--primary); font-weight:600;">
                                {{ $e->ticketTypes->sum('booked_seats') }}
                            </span>
                            <span style="color:var(--text-muted); font-size:0.85rem;">
                                / {{ $e->ticketTypes->sum('total_seats') }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <a href="{{ route('organizer.edit', $e->id) }}" class="btn btn-sm btn-outline">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="{{ route('events.show', $e->id) }}" class="btn btn-sm" style="background:var(--dark); border:1px solid var(--border);">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color:var(--text-muted); padding:2rem;">
                            No events yet.
                            <a href="{{ route('organizer.create') }}" style="color:var(--primary)">Create your first event →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection