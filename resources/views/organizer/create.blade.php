@extends('layouts.app')
@section('title', isset($event) ? 'Edit Event' : 'Create Event — EventHive')
@section('content')

<div class="dashboard-layout">
    <div class="sidebar">
        <div class="logo">Event<span style="color:var(--secondary)">Hive</span></div>
        <nav class="sidebar-nav">
            <a href="{{ route('organizer.dashboard') }}">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="{{ route('organizer.create') }}" class="active">
                <i class="fa-solid fa-calendar-plus"></i> Create Event
            </a>
            <a href="{{ route('organizer.events') }}">
                <i class="fa-solid fa-list"></i> My Events
            </a>
            <a href="{{ route('organizer.attendees') }}">
                <i class="fa-solid fa-users"></i> Attendees
            </a>
            <a href="{{ route('events.index') }}">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Browse Events
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
        <div class="dash-header">
            <h1>{{ isset($event) ? 'Edit Event' : 'Create New Event' }}</h1>
            <p>{{ isset($event) ? 'Update your event details' : 'Fill in the details to publish your event' }}</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST"
              action="{{ isset($event) ? route('organizer.update', $event->id) : route('organizer.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if(isset($event)) @method('PUT') @endif

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Event Title <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="title" placeholder="e.g. Dhaka Music Festival 2026"
                           value="{{ old('title', $event->title ?? '') }}" required>
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Description <span style="color:var(--danger)">*</span></label>
                    <textarea name="description" rows="4" placeholder="Tell attendees what to expect..." required>{{ old('description', $event->description ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Category <span style="color:var(--danger)">*</span></label>
                    <select name="category" required>
                        <option value="">Select category</option>
                        @foreach(['Concert','Workshop','Conference','Food','Sports','Comedy','Tech','Art'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $event->category ?? '') === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        @foreach(['draft','published','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status', $event->status ?? 'draft') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Venue <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="venue" placeholder="e.g. Bashundhara Convention Center"
                           value="{{ old('venue', $event->venue ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>City <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="city" placeholder="e.g. Dhaka"
                           value="{{ old('city', $event->city ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Start Date & Time <span style="color:var(--danger)">*</span></label>
                    <input type="datetime-local" name="event_date"
                           value="{{ old('event_date', isset($event) ? $event->event_date->format('Y-m-d\TH:i') : '') }}" required>
                </div>
                <div class="form-group">
                    <label>End Date & Time</label>
                    <input type="datetime-local" name="event_end"
                           value="{{ old('event_end', isset($event) && $event->event_end ? $event->event_end->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Banner Image</label>
                    <input type="file" name="banner" accept="image/*">
                    @if(isset($event) && $event->banner_image)
                        <div style="margin-top:0.5rem; font-size:0.85rem; color:var(--text-muted);">
                            Current: <span style="color:var(--primary)">{{ $event->banner_image }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TICKET TYPES -->
            <div style="margin-top:1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <h3 style="font-size:1rem;">
                        <i class="fa-solid fa-ticket" style="color:var(--primary)"></i> Ticket Types
                    </h3>
                    <button type="button" onclick="addTicketRow()" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-plus"></i> Add Ticket Type
                    </button>
                </div>
                <div id="ticket-rows">
                    @if(isset($event) && $event->ticketTypes->count())
                        @foreach($event->ticketTypes as $t)
                        <div class="ticket-row" style="display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:0.75rem; margin-bottom:0.75rem; align-items:end;">
                            <div class="form-group" style="margin:0;">
                                <label>Ticket Name</label>
                                <input type="text" name="ticket_name[]" value="{{ $t->name }}">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label>Price (৳)</label>
                                <input type="number" name="ticket_price[]" min="0" value="{{ $t->price }}">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label>Total Seats</label>
                                <input type="number" name="ticket_seats[]" min="1" value="{{ $t->total_seats }}">
                            </div>
                            <button type="button" onclick="this.closest('.ticket-row').remove()" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="ticket-row" style="display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:0.75rem; margin-bottom:0.75rem; align-items:end;">
                            <div class="form-group" style="margin:0;">
                                <label>Ticket Name</label>
                                <input type="text" name="ticket_name[]" placeholder="e.g. General">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label>Price (৳)</label>
                                <input type="number" name="ticket_price[]" placeholder="0 = Free" min="0">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label>Total Seats</label>
                                <input type="number" name="ticket_seats[]" placeholder="100" min="1">
                            </div>
                            <button type="button" onclick="this.closest('.ticket-row').remove()" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div style="margin-top:1.5rem; display:flex; gap:1rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-{{ isset($event) ? 'floppy-disk' : 'rocket' }}"></i>
                    {{ isset($event) ? 'Save Changes' : 'Publish Event' }}
                </button>
                <a href="{{ route('organizer.events') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function addTicketRow() {
    const row = document.createElement('div');
    row.className = 'ticket-row';
    row.style.cssText = 'display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:0.75rem; margin-bottom:0.75rem; align-items:end;';
    row.innerHTML = `
        <div class="form-group" style="margin:0;">
            <label>Ticket Name</label>
            <input type="text" name="ticket_name[]" placeholder="e.g. VIP">
        </div>
        <div class="form-group" style="margin:0;">
            <label>Price (৳)</label>
            <input type="number" name="ticket_price[]" placeholder="0 = Free" min="0">
        </div>
        <div class="form-group" style="margin:0;">
            <label>Total Seats</label>
            <input type="number" name="ticket_seats[]" placeholder="100" min="1">
        </div>
        <button type="button" onclick="this.closest('.ticket-row').remove()" class="btn btn-danger btn-sm">
            <i class="fa-solid fa-trash"></i>
        </button>
    `;
    document.getElementById('ticket-rows').appendChild(row);
}
</script>
@endsection