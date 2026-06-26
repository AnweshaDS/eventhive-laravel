<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EventHive — Discover, Host & Attend Events Near You')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="logo">Event<span>Hive</span></a>
    <div class="nav-links">
        <a href="{{ route('events.index') }}">Browse Events</a>
        @auth
            @if(auth()->user()->role === 'organizer')
                <a href="{{ route('organizer.dashboard') }}">Dashboard</a>
            @elseif(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Admin</a>
            @else
                <a href="{{ route('bookings.index') }}">My Tickets</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-outline">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a>
        @endauth
    </div>
</nav>

@if(session('success'))
    <div class="alert alert-success" style="max-width:1200px; margin:1rem auto; padding:0.8rem 1.2rem;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="max-width:1200px; margin:1rem auto; padding:0.8rem 1.2rem;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
    </div>
@endif

@yield('content')

<footer class="footer">
    <p>© 2025 <span>EventHive</span> — Discover, Host & Attend Events Near You</p>
</footer>

<script src="{{ asset('js/main.js') }}"></script>
@yield('scripts')
</body>
</html>