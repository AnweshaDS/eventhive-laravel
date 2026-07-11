@extends('layouts.app')

@section('title', $event->title . ' — EventHive')

@section('content')

<div style="max-width:1100px; margin:2rem auto; padding:0 2rem;">
    <a href="{{ route('events.index') }}" style="color:var(--text-muted); font-size:0.9rem;">
        <i class="fa-solid fa-arrow-left"></i> Back to Events
    </a>

    <div style="display:grid; grid-template-columns:1fr 340px; gap:2rem; margin-top:1.5rem;">

        <!-- LEFT -->
        <div>
            <!-- Banner -->
            <div style="width:100%; height:320px; border-radius:var(--radius); overflow:hidden; background:linear-gradient(135deg,var(--primary),var(--secondary)); display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                @if($event->banner_image)
                    <img src="{{ asset('images/' . $event->banner_image) }}"
                         style="width:100%;height:100%;object-fit:cover;" alt="{{ $event->title }}">
                @else
                    <i class="fa-solid {{ $icons[$event->category] ?? 'fa-calendar-days' }}"
                       style="font-size:5rem; color:rgba(255,255,255,0.8)"></i>
                @endif
            </div>

            <!-- Title -->
            <div style="margin-bottom:1.5rem;">
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem; flex-wrap:wrap;">
                    <span class="badge badge-primary">
                        <i class="fa-solid {{ $icons[$event->category] ?? 'fa-calendar-days' }}"></i>
                        {{ $event->category }}
                    </span>
                    @if($avg_rating)
                    <span style="color:var(--warning); font-size:0.9rem;">
                        <i class="fa-solid fa-star"></i> {{ round($avg_rating, 1) }}
                        ({{ $event->reviews->count() }} reviews)
                    </span>
                    @endif
                </div>
                <h1 style="font-size:2rem; line-height:1.2; margin-bottom:1rem;">{{ $event->title }}</h1>
                <div style="display:flex; flex-direction:column; gap:0.6rem; color:var(--text-muted); font-size:0.95rem;">
                    <span><i class="fa-regular fa-calendar" style="color:var(--primary); width:18px"></i>
                        {{ $event->event_date->format('l, F j, Y') }}</span>
                    <span><i class="fa-regular fa-clock" style="color:var(--primary); width:18px"></i>
                        {{ $event->event_date->format('g:i A') }}
                        — {{ $event->event_end ? $event->event_end->format('g:i A') : 'TBD' }}</span>
                    <span><i class="fa-solid fa-location-dot" style="color:var(--primary); width:18px"></i>
                        {{ $event->venue }}, {{ $event->city }}</span>
                    <span><i class="fa-regular fa-user" style="color:var(--primary); width:18px"></i>
                        Organized by <strong style="color:var(--text-light)">{{ $event->organizer->name }}</strong></span>
                </div>
            </div>

            {{-- WEATHER WIDGET --}}
            @if($weather)
            <div style="background:linear-gradient(135deg, #0f3460, #16213e); border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem 1.5rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                <img src="{{ $weather['icon_url'] }}" alt="{{ $weather['description'] }}"
                     style="width:60px; height:60px;">
                <div style="flex:1;">
                    <div style="font-size:0.8rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:0.3rem;">
                        <i class="fa-solid fa-cloud-sun" style="color:var(--primary)"></i>
                         Weather {{ now()->diffInDays($event->event_date, false) >= 0 ? 'Forecast for Event Day' : 'Currently in ' . $event->city }}
                    </div>
                <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                    <span style="font-size:2rem; font-weight:700; color:var(--white);">{{ $weather['temp'] }}°C</span>
                    <div>
                        <div style="font-size:0.9rem; color:var(--text-light);">{{ $weather['description'] }}</div>
                        <div style="font-size:0.82rem; color:var(--text-muted);">
                             Feels like {{ $weather['feels_like'] }}°C
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:flex; gap:1.5rem; font-size:0.85rem; color:var(--text-muted);">
                <div style="text-align:center;">
                    <i class="fa-solid fa-droplet" style="color:#3b9edd; display:block; margin-bottom:3px;"></i>
                    {{ $weather['humidity'] }}%<br>Humidity
                </div>
                <div style="text-align:center;">
                    <i class="fa-solid fa-wind" style="color:#6c63ff; display:block; margin-bottom:3px;"></i>
                    {{ $weather['wind_speed'] }} km/h<br>Wind
                </div>
            </div>  
            </div>
        </div>
    </div>
    @endif

            <!-- Description -->
            <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem; margin-bottom:1.5rem;">
                <h3 style="margin-bottom:1rem;">
                    <i class="fa-solid fa-align-left" style="color:var(--primary)"></i> About this Event
                </h3>
                <p style="color:var(--text-muted); line-height:1.8;">{{ $event->description }}</p>
            </div>

            <!-- Reviews -->
            <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem;">
                <h3 style="margin-bottom:1rem;">
                    <i class="fa-solid fa-star" style="color:var(--warning)"></i> Reviews
                </h3>
                @if($event->reviews->isEmpty())
                    <p style="color:var(--text-muted); font-size:0.9rem;">No reviews yet.</p>
                @else
                    @foreach($event->reviews as $review)
                    <div style="padding:1rem 0; border-bottom:1px solid var(--border);">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem;">
                            <strong style="font-size:0.95rem;">{{ $review->user->name }}</strong>
                            <span style="color:var(--warning);">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                            </span>
                        </div>
                        <p style="color:var(--text-muted); font-size:0.88rem;">{{ $review->comment }}</p>
                        <span style="font-size:0.78rem; color:var(--text-muted);">
                            {{ $review->created_at->format('M j, Y') }}
                        </span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- RIGHT: BOOKING PANEL -->
        <div>
            <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem; position:sticky; top:80px;">
                <h3 style="margin-bottom:1.2rem;">
                    <i class="fa-solid fa-ticket" style="color:var(--primary)"></i> Select Tickets
                </h3>

                @if($event->ticketTypes->isEmpty())
                    <p style="color:var(--text-muted); font-size:0.9rem;">No ticket types available yet.</p>
                @else
                    <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:1.5rem;" id="ticket-list">
                        @foreach($event->ticketTypes as $t)
                        <div class="ticket-option {{ $t->seats_left <= 0 ? 'disabled' : '' }}"
                             data-id="{{ $t->id }}"
                             data-price="{{ $t->price }}"
                             data-name="{{ $t->name }}"
                             data-left="{{ $t->seats_left }}"
                             {{ $t->seats_left > 0 ? 'onclick=selectTicket(this)' : '' }}>
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <strong>{{ $t->name }}</strong>
                                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">
                                        @if($t->seats_left <= 0)
                                            <span style="color:var(--danger)"><i class="fa-solid fa-circle-xmark"></i> Sold Out</span>
                                        @elseif($t->seats_left <= 10)
                                            <span style="color:var(--warning)"><i class="fa-solid fa-triangle-exclamation"></i> Only {{ $t->seats_left }} left</span>
                                        @else
                                            <span style="color:var(--success)"><i class="fa-solid fa-circle-check"></i> {{ $t->seats_left }} available</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="price-tag {{ $t->price == 0 ? 'free' : '' }}" style="font-size:1.1rem;">
                                    {{ $t->price == 0 ? 'Free' : '৳ ' . number_format($t->price, 0) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div id="booking-summary" style="display:none;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
                            <label style="font-size:0.9rem; color:var(--text-muted);">Quantity</label>
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <button onclick="changeQty(-1)" class="btn btn-outline btn-sm"
                                    style="width:32px; padding:0; text-align:center;">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <span id="qty-display" style="font-size:1.1rem; font-weight:600;">1</span>
                                <button onclick="changeQty(1)" class="btn btn-outline btn-sm"
                                    style="width:32px; padding:0; text-align:center;">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:1rem; background:rgba(108,99,255,0.08); border-radius:8px; margin-bottom:1rem;">
                            <span style="color:var(--text-muted);">Total</span>
                            <strong id="total-display" style="color:var(--primary); font-size:1.2rem;">৳ 0</strong>
                        </div>
                        @auth
                            <form action="{{ route('bookings.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <input type="hidden" name="ticket_type_id" id="selected-ticket-id" value="">
                                <input type="hidden" name="quantity" id="selected-qty" value="1">
                                <button type="submit" class="btn btn-primary" style="width:100%;">
                                    <i class="fa-solid fa-ticket"></i> Confirm Booking
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary"
                               style="width:100%; text-align:center; display:block;">
                                <i class="fa-solid fa-right-to-bracket"></i> Login to Book
                            </a>
                            <p style="text-align:center; font-size:0.82rem; color:var(--text-muted); margin-top:0.75rem;">
                                <a href="{{ route('register') }}" style="color:var(--primary);">Create a free account</a> to book tickets
                            </p>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.ticket-option {
    padding: 1rem;
    border: 2px solid var(--border);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.ticket-option:hover:not(.disabled) { border-color: var(--primary); background: rgba(108,99,255,0.05); }
.ticket-option.selected { border-color: var(--primary); background: rgba(108,99,255,0.1); }
.ticket-option.disabled { opacity: 0.5; cursor: not-allowed; }
</style>

@endsection

@section('scripts')
<script>
let selectedPrice = 0;
let selectedLeft  = 0;
let qty = 1;

function selectTicket(el) {
    document.querySelectorAll('.ticket-option').forEach(t => t.classList.remove('selected'));
    el.classList.add('selected');
    selectedPrice = parseFloat(el.dataset.price);
    selectedLeft  = parseInt(el.dataset.left);
    qty = 1;
    document.getElementById('qty-display').textContent = qty;
    document.getElementById('selected-ticket-id').value = el.dataset.id;
    document.getElementById('selected-qty').value = qty;
    updateTotal();
    document.getElementById('booking-summary').style.display = 'block';
}

function changeQty(delta) {
    qty = Math.max(1, Math.min(selectedLeft, qty + delta));
    document.getElementById('qty-display').textContent = qty;
    document.getElementById('selected-qty').value = qty;
    updateTotal();
}

function updateTotal() {
    const total = selectedPrice * qty;
    document.getElementById('total-display').textContent =
        selectedPrice === 0 ? 'Free' : '৳ ' + total.toLocaleString();
}
</script>
@endsection