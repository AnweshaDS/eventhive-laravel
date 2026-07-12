@extends('layouts.app')
@section('title', 'My Profile — EventHive')
@section('content')

<div style="max-width:700px; margin:2rem auto; padding:0 2rem;">

    <div style="margin-bottom:2rem;">
        <h1 style="font-size:1.8rem;">
            <i class="fa-solid fa-user-pen" style="color:var(--primary)"></i> My Profile
        </h1>
        <p style="color:var(--text-muted);">Update your personal information and password</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <!-- PROFILE INFO -->
    <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1.5rem; font-size:1.1rem;">
            <i class="fa-solid fa-circle-info" style="color:var(--primary)"></i> Personal Information
        </h3>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Full Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="name"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span style="color:var(--danger); font-size:0.82rem;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Email Address <span style="color:var(--danger)">*</span></label>
                    <input type="email" name="email"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span style="color:var(--danger); font-size:0.82rem;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone"
                           placeholder="+880 1XXX XXXXXX"
                           value="{{ old('phone', $user->phone) }}">
                </div>
            </div>

            <!-- Role badge — read only -->
            <div style="margin-bottom:1.5rem;">
                <label style="font-size:0.9rem; color:var(--text-muted); display:block; margin-bottom:0.4rem;">Account Role</label>
                @php $badge = match($user->role) { 'admin'=>'badge-danger','organizer'=>'badge-primary', default=>'badge-success' }; @endphp
                <span class="badge {{ $badge }}" style="font-size:0.85rem; padding:0.4rem 1rem;">
                    {{ ucfirst($user->role) }}
                </span>
            </div>

            <div style="display:flex; gap:1rem; align-items:center;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
                <span style="font-size:0.82rem; color:var(--text-muted);">
                    Member since {{ $user->created_at->format('M Y') }}
                </span>
            </div>
        </form>
    </div>

    <!-- CHANGE PASSWORD -->
    <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:2rem; margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1.5rem; font-size:1.1rem;">
            <i class="fa-solid fa-lock" style="color:var(--primary)"></i> Change Password
        </h3>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Current Password <span style="color:var(--danger)">*</span></label>
                <div class="input-icon">
                    <input type="password" name="current_password"
                           id="current_password" placeholder="Your current password" required>
                    <i class="fa-regular fa-eye toggle-pass"
                       onclick="togglePass('current_password', this)"></i>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label>New Password <span style="color:var(--danger)">*</span></label>
                    <div class="input-icon">
                        <input type="password" name="password"
                               id="new_password" placeholder="Min 8 characters" required>
                        <i class="fa-regular fa-eye toggle-pass"
                           onclick="togglePass('new_password', this)"></i>
                    </div>
                    @error('password')
                        <span style="color:var(--danger); font-size:0.82rem;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Confirm New Password <span style="color:var(--danger)">*</span></label>
                    <div class="input-icon">
                        <input type="password" name="password_confirmation"
                               id="confirm_password" placeholder="Repeat new password" required>
                        <i class="fa-regular fa-eye toggle-pass"
                           onclick="togglePass('confirm_password', this)"></i>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-key"></i> Update Password
            </button>
        </form>
    </div>

    <!-- QUICK LINKS -->
    <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem;">
        <h3 style="margin-bottom:1rem; font-size:1rem;">
            <i class="fa-solid fa-link" style="color:var(--primary)"></i> Quick Links
        </h3>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            @if(auth()->user()->role === 'attendee')
                <a href="{{ route('bookings.index') }}" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-ticket"></i> My Tickets
                </a>
            @elseif(auth()->user()->role === 'organizer')
                <a href="{{ route('organizer.dashboard') }}" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="{{ route('organizer.create') }}" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-calendar-plus"></i> Create Event
                </a>
            @elseif(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-gauge"></i> Admin Panel
                </a>
            @endif
            <a href="{{ route('events.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-magnifying-glass"></i> Browse Events
            </a>
        </div>
    </div>
</div>

<style>
.input-icon { position: relative; }
.input-icon input { padding-right: 2.5rem; }
.toggle-pass {
    position: absolute; right: 0.9rem; top: 50%;
    transform: translateY(-50%); cursor: pointer;
    color: var(--text-muted); font-size: 0.9rem;
}
</style>

@endsection

@section('scripts')
<script>
function togglePass(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endsection