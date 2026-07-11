@extends('layouts.app')
@section('title', 'Events — EventHive Admin')
@section('content')

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="logo">Event<span style="color:var(--secondary)">Hive</span></div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="{{ route('admin.events') }}" class="active">
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <h1 style="font-size:1.6rem;">All Events</h1>
                <p style="color:var(--text-muted);">{{ $events->count() }} event{{ $events->count() !== 1 ? 's' : '' }} found</p>
            </div>
            <form method="GET" action="{{ route('admin.events') }}" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <input type="text" name="search" placeholder="Search title, city..."
                    value="{{ $search }}"
                    style="background:var(--card-bg); border:1px solid var(--border); color:var(--white); padding:0.5rem 1rem; border-radius:8px; outline:none; min-width:220px;">
                <select name="status" onchange="this.form.submit()"
                    style="background:var(--card-bg); border:1px solid var(--border); color:var(--white); padding:0.5rem 1rem; border-radius:8px; outline:none;">
                    <option value="">All Status</option>
                    <option value="published" {{ $filter==='published' ? 'selected':'' }}>Published</option>
                    <option value="draft"     {{ $filter==='draft'     ? 'selected':'' }}>Draft</option>
                    <option value="cancelled" {{ $filter==='cancelled' ? 'selected':'' }}>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <a href="{{ route('admin.events') }}" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Organizer</th>
                        <th>Date</th>
                        <th>Bookings</th>
                        <th>Revenue</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $e)
                    <tr>
                        <td>
                            <strong style="font-size:0.9rem;">{{ $e->title }}</strong>
                            <div style="font-size:0.78rem; color:var(--text-muted);">
                                <i class="fa-solid fa-location-dot"></i> {{ $e->city }}
                            </div>
                        </td>
                        <td style="font-size:0.85rem; color:var(--text-muted);">{{ $e->organizer->name }}</td>
                        <td style="font-size:0.82rem; color:var(--text-muted);">{{ $e->event_date->format('M j, Y') }}</td>
                        <td>
                            <span style="color:var(--primary); font-weight:600;">{{ $e->ticketTypes->sum('booked_seats') }}</span>
                            <span style="color:var(--text-muted); font-size:0.82rem;"> / {{ $e->ticketTypes->sum('total_seats') }}</span>
                        </td>
                        <td style="color:var(--success); font-weight:600; font-size:0.88rem;">
                            ৳{{ number_format($e->ticketTypes->sum(fn($t) => $t->booked_seats * $t->price), 0) }}
                        </td>
                        <td>
                            @php $badge = match($e->status) { 'published'=>'badge-success','draft'=>'badge-warning','cancelled'=>'badge-danger', default=>'badge-primary' }; @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($e->status) }}</span>
                        </td>
                        <td>
                            <div style="display:flex; gap:0.4rem; flex-wrap:wrap; align-items:center;">
                                <form method="POST" action="{{ route('admin.events.status') }}"
                                      style="display:flex; gap:0.3rem; align-items:center;">
                                    @csrf
                                    <input type="hidden" name="event_id" value="{{ $e->id }}">
                                    <select name="new_status"
                                        style="background:var(--dark); border:1px solid var(--border); color:var(--white); padding:0.25rem 0.4rem; border-radius:6px; font-size:0.8rem; outline:none;">
                                        <option value="published" {{ $e->status==='published' ? 'selected':'' }}>Published</option>
                                        <option value="draft"     {{ $e->status==='draft'     ? 'selected':'' }}>Draft</option>
                                        <option value="cancelled" {{ $e->status==='cancelled' ? 'selected':'' }}>Cancelled</option>
                                    </select>
                                    <button type="submit" name="change_status" class="btn btn-sm btn-outline">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                <a href="{{ route('events.show', $e->id) }}"
                                   class="btn btn-sm" style="background:var(--dark); border:1px solid var(--border);">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.events.delete', $e->id) }}"
                                      onsubmit="return confirm('Delete this event permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:var(--text-muted); padding:2rem;">No events found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection