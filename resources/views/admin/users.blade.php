@extends('layouts.app')
@section('title', 'Users — EventHive Admin')
@section('content')

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="logo">Event<span style="color:var(--secondary)">Hive</span></div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="active">
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <h1 style="font-size:1.6rem;">Users</h1>
                <p style="color:var(--text-muted);">{{ $users->count() }} user{{ $users->count() !== 1 ? 's' : '' }} found</p>
            </div>
            <form method="GET" action="{{ route('admin.users') }}" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <input type="text" name="search" placeholder="Search name or email..."
                    value="{{ $search }}"
                    style="background:var(--card-bg); border:1px solid var(--border); color:var(--white); padding:0.5rem 1rem; border-radius:8px; outline:none; min-width:200px;">
                <select name="role" onchange="this.form.submit()"
                    style="background:var(--card-bg); border:1px solid var(--border); color:var(--white); padding:0.5rem 1rem; border-radius:8px; outline:none;">
                    <option value="">All Roles</option>
                    <option value="attendee"  {{ $filter==='attendee'  ? 'selected':'' }}>Attendee</option>
                    <option value="organizer" {{ $filter==='organizer' ? 'selected':'' }}>Organizer</option>
                    <option value="admin"     {{ $filter==='admin'     ? 'selected':'' }}>Admin</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm">
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
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td style="color:var(--text-muted); font-size:0.85rem;">{{ $u->id }}</td>
                        <td>
                            <strong>{{ $u->name }}</strong>
                            @if($u->phone)
                                <div style="font-size:0.78rem; color:var(--text-muted);">{{ $u->phone }}</div>
                            @endif
                        </td>
                        <td style="font-size:0.88rem; color:var(--text-muted);">{{ $u->email }}</td>
                        <td>
                            @php $badge = match($u->role) { 'admin'=>'badge-danger','organizer'=>'badge-primary', default=>'badge-success' }; @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td style="font-size:0.82rem; color:var(--text-muted);">
                            {{ $u->created_at->format('M j, Y') }}
                        </td>
                        <td>
                            <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                                <form method="POST" action="{{ route('admin.users.role') }}"
                                      style="display:flex; gap:0.4rem; align-items:center;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $u->id }}">
                                    <select name="new_role"
                                        style="background:var(--dark); border:1px solid var(--border); color:var(--white); padding:0.25rem 0.5rem; border-radius:6px; font-size:0.82rem; outline:none;">
                                        <option value="attendee"  {{ $u->role==='attendee'  ? 'selected':'' }}>Attendee</option>
                                        <option value="organizer" {{ $u->role==='organizer' ? 'selected':'' }}>Organizer</option>
                                        <option value="admin"     {{ $u->role==='admin'     ? 'selected':'' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline" title="Change role">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.delete', $u->id) }}"
                                      onsubmit="return confirm('Delete this user permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection