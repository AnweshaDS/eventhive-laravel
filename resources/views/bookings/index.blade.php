@extends('layouts.app')

@section('title', 'My Tickets — EventHive')

@section('content')

<div style="max-width:900px; margin:2rem auto; padding:0 2rem;">

    <div style="margin-bottom:2rem;">
        <h1 style="font-size:1.8rem;">
            <i class="fa-solid fa-ticket" style="color:var(--primary)"></i> My Tickets
        </h1>
        <p style="color:var(--text-muted);">All your booked event tickets in one place</p>
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

    @if($bookings->isEmpty())
        <div style="text-align:center; padding:4rem; color:var(--text-muted); background:var(--card-bg); border-radius:var(--radius); border:1px solid var(--border);">
            <i class="fa-solid fa-ticket-simple" style="font-size:3rem; display:block; margin-bottom:1rem; color:var(--border)"></i>
            <h3 style="margin-bottom:0.5rem;">No tickets yet</h3>
            <p style="margin-bottom:1.5rem;">Browse events and book your first ticket!</p>
            <a href="{{ route('events.index') }}" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Browse Events
            </a>
        </div>
    @else

    {{-- TABS --}}
    <div style="display:flex; gap:0.5rem; margin-bottom:1.5rem; border-bottom:1px solid var(--border); padding-bottom:0;">
        <button onclick="switchTab('active')" id="tab-active"
            style="padding:0.6rem 1.2rem; border:none; background:none; color:var(--primary); font-weight:600; border-bottom:2px solid var(--primary); cursor:pointer; font-size:0.9rem;">
            <i class="fa-solid fa-circle-check"></i> Active Tickets
        </button>
        <button onclick="switchTab('cancelled')" id="tab-cancelled"
            style="padding:0.6rem 1.2rem; border:none; background:none; color:var(--text-muted); cursor:pointer; font-size:0.9rem;">
            <i class="fa-solid fa-circle-xmark"></i> Cancelled
        </button>
    </div>

    {{-- ACTIVE BOOKINGS --}}
    <div id="section-active">
        @php $active = $bookings->where('booking_status', 'confirmed'); @endphp
        @if($active->isEmpty())
            <div style="text-align:center; padding:3rem; color:var(--text-muted);">
                <p>No active bookings.</p>
            </div>
        @else
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            @foreach($active as $b)
            @php
                $event     = $b->ticketType->event;
                $days_left = now()->diffInDays($event->event_date, false);
                $can_cancel = $days_left >= 7;
                $icons = ['Concert'=>'fa-music','Workshop'=>'fa-screwdriver-wrench','Conference'=>'fa-briefcase','Food'=>'fa-utensils','Sports'=>'fa-futbol','Comedy'=>'fa-face-laugh','Tech'=>'fa-laptop-code','Art'=>'fa-palette'];
            @endphp
            <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden;">

                {{-- Header --}}
                <div style="background:linear-gradient(135deg, var(--dark-2), #1a0533); padding:1.2rem 1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
                    <div>
                        <div style="font-size:0.8rem; color:var(--primary); font-weight:600; text-transform:uppercase; letter-spacing:1px; margin-bottom:0.3rem;">
                            <i class="fa-solid {{ $icons[$event->category] ?? 'fa-calendar-days' }}"></i>
                            {{ $event->category }}
                        </div>
                        <h3 style="font-size:1.1rem; margin:0;">{{ $event->title }}</h3>
                    </div>
                    <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                        @if($b->qrTicket && $b->qrTicket->scan_status === 'scanned')
                            <span class="badge badge-warning"><i class="fa-solid fa-qrcode"></i> Used</span>
                        @else
                            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Valid</span>
                        @endif
                        <span class="badge badge-primary">{{ $b->ticketType->name }}</span>
                    </div>
                </div>

                {{-- Body --}}
                <div style="display:grid; grid-template-columns:1fr auto; gap:1.5rem; padding:1.5rem;">
                    <div style="display:flex; flex-direction:column; gap:0.6rem;">
                        <div style="display:flex; gap:0.75rem; color:var(--text-muted); font-size:0.9rem;">
                            <span style="min-width:20px; color:var(--primary); text-align:center;"><i class="fa-regular fa-calendar"></i></span>
                            {{ $event->event_date->format('l, F j, Y · g:i A') }}
                        </div>
                        <div style="display:flex; gap:0.75rem; color:var(--text-muted); font-size:0.9rem;">
                            <span style="min-width:20px; color:var(--primary); text-align:center;"><i class="fa-solid fa-location-dot"></i></span>
                            {{ $event->venue }}, {{ $event->city }}
                        </div>
                        <div style="display:flex; gap:0.75rem; color:var(--text-muted); font-size:0.9rem;">
                            <span style="min-width:20px; color:var(--primary); text-align:center;"><i class="fa-solid fa-layer-group"></i></span>
                            {{ $b->quantity }} × {{ $b->ticketType->name }} ticket{{ $b->quantity > 1 ? 's' : '' }}
                        </div>
                        <div style="display:flex; gap:0.75rem; font-size:0.9rem;">
                            <span style="min-width:20px; color:var(--primary); text-align:center;"><i class="fa-solid fa-tag"></i></span>
                            <strong style="color:var(--success);">
                                {{ $b->total_amount == 0 ? 'Free' : '৳ ' . number_format($b->total_amount, 0) }}
                            </strong>
                        </div>

                        {{-- Days left indicator --}}
                        <div style="display:flex; gap:0.75rem; font-size:0.85rem;">
                            <span style="min-width:20px; color:var(--primary); text-align:center;"><i class="fa-solid fa-clock"></i></span>
                            @if($days_left < 0)
                                <span style="color:var(--text-muted);">Event has passed</span>
                            @elseif($days_left == 0)
                                <span style="color:var(--warning); font-weight:600;">Event is today!</span>
                            @elseif($days_left <= 7)
                                <span style="color:var(--warning);">{{ $days_left }} day(s) until event</span>
                            @else
                                <span style="color:var(--success);">{{ $days_left }} days until event</span>
                            @endif
                        </div>

                        <div style="margin-top:0.5rem; padding-top:0.75rem; border-top:1px solid var(--border); font-size:0.8rem; color:var(--text-muted);">
                            @if($b->qrTicket)
                                <i class="fa-solid fa-key"></i> Token:
                                <code style="color:var(--primary); font-size:0.82rem;">{{ $b->qrTicket->verify_token }}</code>
                            @endif
                            &nbsp;·&nbsp; Booked {{ $b->created_at->format('M j, Y') }}
                        </div>

                        {{-- Action buttons --}}
                        <div style="display:flex; gap:0.75rem; margin-top:0.5rem; flex-wrap:wrap; align-items:center;">
                            @if($b->qrTicket)
                            <a href="{{ route('qr.verify', ['token' => $b->qrTicket->verify_token]) }}"
                               target="_blank" class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-shield-halved"></i> Verify
                            </a>
                            @endif
                            <a href="{{ route('events.show', $event->id) }}"
                               class="btn btn-sm" style="background:var(--dark); border:1px solid var(--border);">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Event
                            </a>

                            {{-- Cancel button --}}
                            @if($can_cancel)
                                <form method="POST"
                                      action="{{ route('bookings.cancel', $b->id) }}"
                                      onsubmit="return confirm('Cancel this booking?{{ $b->total_amount > 0 ? ' You will receive a refund of ৳' . number_format($b->total_amount, 0) . '.' : '' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-xmark"></i> Cancel Booking
                                    </button>
                                </form>
                            @elseif($days_left >= 0)
                                <span style="font-size:0.78rem; color:var(--text-muted); display:flex; align-items:center; gap:0.3rem;">
                                    <i class="fa-solid fa-lock"></i>
                                    Cancellation closed (less than 7 days)
                                </span>
                            @endif
                        </div>

                        {{-- Refund notice --}}
                        @if($b->total_amount > 0 && $can_cancel)
                        <div style="font-size:0.78rem; color:var(--text-muted); background:rgba(46,204,113,0.08); border:1px solid rgba(46,204,113,0.2); border-radius:6px; padding:0.5rem 0.75rem; margin-top:0.25rem;">
                            <i class="fa-solid fa-rotate-left" style="color:var(--success)"></i>
                            Full refund of <strong style="color:var(--success)">৳{{ number_format($b->total_amount, 0) }}</strong> will be processed within 3-5 business days.
                        </div>
                        @endif
                    </div>

                    {{-- QR Code --}}
                    <div style="text-align:center;">
                        @if($b->qrTicket)
                            <div style="background:white; padding:10px; border-radius:8px; display:inline-block;">
                                <img src="{{ asset('images/qrcodes/' . $b->qrTicket->qr_code) }}"
                                     alt="QR Code" style="width:130px; height:130px; display:block;">
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.5rem;">
                                <i class="fa-solid fa-qrcode"></i> Show at entry
                            </div>
                            <a href="{{ asset('images/qrcodes/' . $b->qrTicket->qr_code) }}"
                               download="EventHive-Ticket-{{ $b->id }}.png"
                               class="btn btn-sm btn-primary" style="margin-top:0.5rem;">
                                <i class="fa-solid fa-download"></i> Download
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- CANCELLED BOOKINGS --}}
    <div id="section-cancelled" style="display:none;">
        @php $cancelled = $bookings->where('booking_status', 'cancelled'); @endphp
        @if($cancelled->isEmpty())
            <div style="text-align:center; padding:3rem; color:var(--text-muted);">
                <i class="fa-solid fa-circle-check" style="font-size:2rem; display:block; margin-bottom:0.5rem; color:var(--success)"></i>
                <p>No cancelled bookings!</p>
            </div>
        @else
        <div style="display:flex; flex-direction:column; gap:1rem;">
            @foreach($cancelled as $b)
            @php
                $event = $b->ticketType->event;
                $icons = ['Concert'=>'fa-music','Workshop'=>'fa-screwdriver-wrench','Conference'=>'fa-briefcase','Food'=>'fa-utensils','Sports'=>'fa-futbol','Comedy'=>'fa-face-laugh','Tech'=>'fa-laptop-code','Art'=>'fa-palette'];
            @endphp
            <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem 1.5rem; opacity:0.75;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
                    <div>
                        <h3 style="font-size:1rem; margin:0 0 0.3rem;">{{ $event->title }}</h3>
                        <div style="font-size:0.85rem; color:var(--text-muted);">
                            <i class="fa-regular fa-calendar"></i> {{ $event->event_date->format('M j, Y') }}
                            &nbsp;·&nbsp;
                            <i class="fa-solid fa-ticket"></i> {{ $b->ticketType->name }}
                            &nbsp;·&nbsp;
                            {{ $b->quantity }} ticket{{ $b->quantity > 1 ? 's' : '' }}
                        </div>
                    </div>
                    <div style="display:flex; gap:0.5rem; align-items:center;">
                        <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Cancelled</span>
                        @if($b->payment_status === 'refunded')
                            <span class="badge badge-success"><i class="fa-solid fa-rotate-left"></i> Refunded</span>
                        @endif
                    </div>
                </div>
                @if($b->payment_status === 'refunded')
                <div style="margin-top:0.75rem; font-size:0.82rem; color:var(--text-muted);">
                    <i class="fa-solid fa-rotate-left" style="color:var(--success)"></i>
                    Refund of <strong style="color:var(--success)">৳{{ number_format($b->total_amount, 0) }}</strong> — cancelled on {{ $b->updated_at->format('M j, Y') }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @endif
</div>

@endsection

@section('scripts')
<script>
function switchTab(tab) {
    document.getElementById('section-active').style.display    = tab === 'active'    ? 'block' : 'none';
    document.getElementById('section-cancelled').style.display = tab === 'cancelled' ? 'block' : 'none';

    document.getElementById('tab-active').style.cssText    = tab === 'active'
        ? 'padding:0.6rem 1.2rem; border:none; background:none; color:var(--primary); font-weight:600; border-bottom:2px solid var(--primary); cursor:pointer; font-size:0.9rem;'
        : 'padding:0.6rem 1.2rem; border:none; background:none; color:var(--text-muted); cursor:pointer; font-size:0.9rem;';

    document.getElementById('tab-cancelled').style.cssText = tab === 'cancelled'
        ? 'padding:0.6rem 1.2rem; border:none; background:none; color:var(--primary); font-weight:600; border-bottom:2px solid var(--primary); cursor:pointer; font-size:0.9rem;'
        : 'padding:0.6rem 1.2rem; border:none; background:none; color:var(--text-muted); cursor:pointer; font-size:0.9rem;';
}
</script>
@endsection