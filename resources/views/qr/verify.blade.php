<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Ticket — EventHive</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem;">
    <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:2.5rem; width:100%; max-width:480px; text-align:center;">

        <a href="{{ route('home') }}" style="color:var(--primary); font-size:1.3rem; font-weight:700;">
            Event<span style="color:var(--secondary)">Hive</span>
        </a>

        @if($booking)
        @php
            $b     = $booking->booking;
            $event = $b->ticketType->event;
        @endphp
            <div style="margin:1.5rem 0;">
                <div style="width:80px; height:80px; background:rgba(46,204,113,0.15); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                    <i class="fa-solid fa-circle-check" style="font-size:2.5rem; color:var(--success);"></i>
                </div>
                <h2 style="color:var(--success); margin-bottom:0.3rem;">Ticket Valid</h2>
                <p style="color:var(--text-muted); font-size:0.9rem;">This ticket is authentic and confirmed</p>
            </div>

            <div style="background:var(--dark); border-radius:var(--radius); padding:1.5rem; text-align:left; margin-bottom:1.5rem;">
                <div style="display:flex; flex-direction:column; gap:0.75rem; font-size:0.9rem;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted)"><i class="fa-solid fa-calendar-days"></i> Event</span>
                        <strong>{{ $event->title }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted)"><i class="fa-regular fa-user"></i> Attendee</span>
                        <strong>{{ $b->user->name }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted)"><i class="fa-solid fa-ticket"></i> Ticket</span>
                        <strong>{{ $b->ticketType->name }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted)"><i class="fa-solid fa-layer-group"></i> Quantity</span>
                        <strong>{{ $b->quantity }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted)"><i class="fa-regular fa-calendar"></i> Date</span>
                        <strong>{{ $event->event_date->format('M j, Y · g:i A') }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted)"><i class="fa-solid fa-location-dot"></i> Venue</span>
                        <strong>{{ $event->venue }}, {{ $event->city }}</strong>
                    </div>
                    <div style="border-top:1px solid var(--border); padding-top:0.75rem; display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted)"><i class="fa-solid fa-shield-halved"></i> Status</span>
                        <span class="badge {{ $booking->scan_status === 'scanned' ? 'badge-warning' : 'badge-success' }}">
                            {{ $booking->scan_status === 'scanned' ? 'Already Scanned' : 'Not Yet Scanned' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($booking->scan_status !== 'scanned' && Auth::check() && Auth::user()->role === 'organizer')
            <form method="POST" action="{{ route('qr.markScanned') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <button type="submit" class="btn btn-success" style="width:100%;">
                    <i class="fa-solid fa-qrcode"></i> Mark as Scanned
                </button>
            </form>
            @endif

        @else
            <div style="margin:1.5rem 0;">
                <div style="width:80px; height:80px; background:rgba(231,76,60,0.15); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                    <i class="fa-solid fa-circle-xmark" style="font-size:2.5rem; color:var(--danger);"></i>
                </div>
                <h2 style="color:var(--danger); margin-bottom:0.3rem;">Invalid Ticket</h2>
                <p style="color:var(--text-muted); font-size:0.9rem;">{{ $error }}</p>
            </div>
        @endif

        <a href="{{ route('home') }}" style="display:block; margin-top:1.5rem; font-size:0.88rem; color:var(--text-muted);">
            ← Back to EventHive
        </a>
    </div>
</div>
</body>
</html>