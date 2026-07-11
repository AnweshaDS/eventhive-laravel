@extends('layouts.app')
@section('title', 'Admin Dashboard — EventHive')
@section('content')

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="logo">Event<span style="color:var(--secondary)">Hive</span></div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="active">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="{{ route('admin.events') }}">
                <i class="fa-solid fa-calendar-days"></i> Events
            </a>
            <a href="{{ route('events.index') }}">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Site
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
            <h1>Admin Dashboard</h1>
            <p>Platform overview — EventHive</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-users"></i> Total Users</div>
                <div class="stat-value">{{ $stats['total_users'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-calendar-days"></i> Total Events</div>
                <div class="stat-value">{{ $stats['total_events'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-ticket"></i> Total Bookings</div>
                <div class="stat-value">{{ $stats['total_bookings'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-bangladeshi-taka-sign"></i> Revenue</div>
                <div class="stat-value">৳{{ number_format($stats['total_revenue'], 0) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-circle-dot"></i> Live Events</div>
                <div class="stat-value">{{ $stats['live_events'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fa-solid fa-building"></i> Organizers</div>
                <div class="stat-value">{{ $stats['total_organizers'] }}</div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; margin-top:1rem;">
            <!-- RECENT BOOKINGS -->
            <div>
                <h2 style="font-size:1.1rem; margin-bottom:1rem;">
                    <i class="fa-solid fa-ticket" style="color:var(--primary)"></i> Recent Bookings
                </h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Attendee</th>
                                <th>Event</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_bookings as $b)
                            <tr>
                                <td>
                                    <strong style="font-size:0.88rem;">{{ $b->user->name }}</strong>
                                    <div style="font-size:0.78rem; color:var(--text-muted);">{{ $b->ticketType->name }}</div>
                                </td>
                                <td style="font-size:0.82rem; color:var(--text-muted);">
                                    {{ Str::limit($b->ticketType->event->title, 25) }}
                                </td>
                                <td style="color:var(--success); font-weight:600; font-size:0.88rem;">
                                    {{ $b->total_amount == 0 ? 'Free' : '৳'.number_format($b->total_amount, 0) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align:center; color:var(--text-muted);">No bookings yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RECENT EVENTS -->
            <div>
                <h2 style="font-size:1.1rem; margin-bottom:1rem;">
                    <i class="fa-solid fa-calendar-days" style="color:var(--primary)"></i> Recent Events
                </h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Organizer</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_events as $e)
                            <tr>
                                <td>
                                    <strong style="font-size:0.88rem;">{{ Str::limit($e->title, 22) }}</strong>
                                    <div style="font-size:0.78rem; color:var(--text-muted);">{{ $e->city }}</div>
                                </td>
                                <td style="font-size:0.82rem; color:var(--text-muted);">{{ $e->organizer->name }}</td>
                                <td>
                                    @php $badge = match($e->status) { 'published'=>'badge-success','draft'=>'badge-warning','cancelled'=>'badge-danger', default=>'badge-primary' }; @endphp
                                    <span class="badge {{ $badge }}">{{ ucfirst($e->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align:center; color:var(--text-muted);">No events yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection