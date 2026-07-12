@extends('layouts.app')

@section('title', 'Register — EventHive')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card" style="max-width:500px;">
        <a href="{{ route('home') }}" style="color:var(--primary); font-size:1.4rem; font-weight:700;">
            Event<span style="color:var(--secondary)">Hive</span>
        </a>
        <h2 style="margin-top:1rem;">Create Account</h2>
        <p>Join thousands discovering amazing events</p>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Role Toggle -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1.5rem;">
                <label class="role-card selected" id="role-attendee" onclick="selectRole('attendee')">
                    <i class="fa-solid fa-user"></i>
                    <span>Attendee</span>
                    <small>Browse & book events</small>
                </label>
                <label class="role-card" id="role-organizer" onclick="selectRole('organizer')">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Organizer</span>
                    <small>Host & manage events</small>
                </label>
            </div>
            <input type="hidden" name="role" id="role-input" value="attendee">

            <div class="form-group">
                <label>Full Name <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" placeholder="John Doe"
                       value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>Email Address <span style="color:var(--danger)">*</span></label>
                <input type="email" name="email" placeholder="you@example.com"
                       value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="+880 1XXX XXXXXX"
                       value="{{ old('phone') }}">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                <div class="form-group">
                    <label>Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="password" placeholder="Min 8 characters" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="password_confirmation"
                           placeholder="Repeat password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:0.5rem;">
                <i class="fa-solid fa-user-plus"></i> Create Account
            </button>
        </form>

        <p style="text-align:center; margin-top:1.2rem; font-size:0.9rem; color:var(--text-muted);">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--primary);">Login here</a>
        </p>
    </div>
</div>

<style>
.role-card {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.3rem; padding: 1rem;
    border: 2px solid var(--border); border-radius: var(--radius);
    cursor: pointer; transition: all 0.2s; text-align: center;
    background: var(--dark);
}
.role-card i    { font-size: 1.5rem; color: var(--text-muted); transition: color 0.2s; }
.role-card span { font-weight: 600; font-size: 0.95rem; }
.role-card small{ font-size: 0.75rem; color: var(--text-muted); }
.role-card.selected { border-color: var(--primary); background: rgba(108,99,255,0.1); }
.role-card.selected i { color: var(--primary); }
</style>

@endsection

@section('scripts')
<script>
function selectRole(role) {
    document.getElementById('role-input').value = role;
    document.getElementById('role-attendee').classList.toggle('selected', role === 'attendee');
    document.getElementById('role-organizer').classList.toggle('selected', role === 'organizer');
}
</script>
@endsection