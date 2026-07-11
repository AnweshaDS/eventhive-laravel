@extends('layouts.app')
@section('title', 'Attendees — EventHive')
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
            <a href="{{ route('organizer.events') }}">
                <i class="fa-solid fa-list"></i> My Events
            </a>
            <a href="{{ route('organizer.attendees') }}" class="active">
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
                <h1 style="font-size:1.6rem;">Attendees</h1>
                <p style="color:var(--text-muted);">{{ $attendees->count() }} confirmed booking{{ $attendees->count() !== 1 ? 's' : '' }}</p>
            </div>
            <form method="GET" action="{{ route('organizer.attendees') }}">
                <select name="event_id" onchange="this.form.submit()"
                    style="background:var(--card-bg); border:1px solid var(--border); color:var(--white); padding:0.5rem 1rem; border-radius:8px; outline:none;">
                    <option value="">All Events</option>
                    @foreach($my_events as $me)
                        <option value="{{ $me->id }}" {{ $event_filter == $me->id ? 'selected' : '' }}>
                            {{ $me->title }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($attendees->isEmpty())
            <div style="text-align:center; padding:4rem; background:var(--card-bg); border-radius:var(--radius); border:1px solid var(--border); color:var(--text-muted);">
                <i class="fa-solid fa-users" style="font-size:3rem; display:block; margin-bottom:1rem;"></i>
                <h3>No attendees yet</h3>
            </div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Attendee</th>
                        <th>Event</th>
                        <th>Ticket</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>QR Status</th>
                        <th>Booked</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendees as $a)
                    <tr>
                        <td>
                            <strong>{{ $a->user->name }}</strong>
                            <div style="font-size:0.8rem; color:var(--text-muted);">{{ $a->user->email }}</div>
                            @if($a->user->phone)
                                <div style="font-size:0.8rem; color:var(--text-muted);">{{ $a->user->phone }}</div>
                            @endif
                        </td>
                        <td style="font-size:0.88rem;">{{ $a->ticketType->event->title }}</td>
                        <td><span class="badge badge-primary">{{ $a->ticketType->name }}</span></td>
                        <td style="text-align:center;">{{ $a->quantity }}</td>
                        <td style="color:var(--success); font-weight:600;">
                            {{ $a->total_amount == 0 ? 'Free' : '৳' . number_format($a->total_amount, 0) }}
                        </td>
                        <td>
                            @if($a->qrTicket && $a->qrTicket->scan_status === 'scanned')
                                <span class="badge badge-warning"><i class="fa-solid fa-qrcode"></i> Scanned</span>
                            @else
                                <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Valid</span>
                            @endif
                        </td>
                        <td style="font-size:0.82rem; color:var(--text-muted);">
                            {{ $a->created_at->format('M j, Y') }}
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