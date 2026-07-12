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
    <div style="display:flex; flex-direction:column; gap:1.5rem;">
        @foreach($bookings as $b)
        @php
            $event  = $b->ticketType->event;
            $icons  = ['Concert'=>'fa-music','Workshop'=>'fa-screwdriver-wrench','Conference'=>'fa-briefcase','Food'=>'fa-utensils','Sports'=>'fa-futbol','Comedy'=>'fa-face-laugh','Tech'=>'fa-laptop-code','Art'=>'fa-palette'];
        @endphp
        <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden;">

            <!-- Header -->
            <div style="background:linear-gradient(135deg, var(--dark-2), #1a0533); padding:1.2rem 1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
                <div>
                    <div style="font-size:0.8rem; color:var(--primary); font-weight:600; text-transform:uppercase; letter-spacing:1px; margin-bottom:0.3rem;">
                        <i class="fa-solid {{ $icons[$event->category] ?? 'fa-calendar-days' }}"></i>
                        {{ $event->category }}
                    </div>
                    <h3 style="font-size:1.1rem; margin:0;">{{ $event->title }}</h3>
                </div>
                <div style="display:flex; gap:0.5rem; align-items:center;">
                    @if($b->qrTicket && $b->qrTicket->scan_status === 'scanned')
                        <span class="badge badge-warning"><i class="fa-solid fa-qrcode"></i> Used</span>
                    @else
                        <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Valid</span>
                    @endif
                    <span class="badge badge-primary">{{ $b->ticketType->name }}</span>
                </div>
            </div>

            <!-- Body -->
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
                    <div style="margin-top:0.5rem; padding-top:0.75rem; border-top:1px solid var(--border); font-size:0.8rem; color:var(--text-muted);">
                        @if($b->qrTicket)
                            <i class="fa-solid fa-key"></i> Token:
                            <code style="color:var(--primary); font-size:0.82rem;">{{ $b->qrTicket->verify_token }}</code>
                        @endif
                        &nbsp;·&nbsp; Booked {{ $b->created_at->format('M j, Y') }}
                    </div>
                    <div style="display:flex; gap:0.75rem; margin-top:0.5rem; flex-wrap:wrap;">
                        @if($b->qrTicket)
                        <a href="{{ route('qr.verify', ['token' => $b->qrTicket->verify_token]) }}"
                           target="_blank" class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-shield-halved"></i> Verify Ticket
                        </a>
                        @endif
                        <a href="{{ route('events.show', $event->id) }}"
                           class="btn btn-sm" style="background:var(--dark); border:1px solid var(--border);">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> View Event
                        </a>
                    </div>
                </div>

                <!-- QR Code -->
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

@endsection