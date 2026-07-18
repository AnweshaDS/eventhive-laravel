@extends('layouts.app')

@section('title', 'Login - EventHive')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <a href="{{ route('home') }}" style="color:var(--primary); font-size:1.4rem; font-weight:700;">
            Event<span style="color:var(--secondary)">Hive</span>
        </a>
        <h2 style="margin-top:1rem;">Welcome Back</h2>
        <p>Login to your EventHive account</p>
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="you@example.com"
                       value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-icon">
                    <input type="password" name="password" id="password"
                           placeholder="Your password" required>
                    <i class="fa-regular fa-eye toggle-pass" onclick="togglePassword()"></i>
                </div>
            </div>
            <div style="display:flex; align-items:center; margin-bottom:1rem;">
                <input type="checkbox" name="remember" id="remember" style="width:auto; margin-right:0.5rem;">
                <label for="remember" style="margin:0; font-size:0.9rem; color:var(--text-muted);">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
        </form>

        <p style="text-align:center; margin-top:1.2rem; font-size:0.9rem; color:var(--text-muted);">
            Don't have an account?
            <a href="{{ route('register') }}" style="color:var(--primary);">Sign up free</a>
        </p>
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
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.querySelector('.toggle-pass');
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