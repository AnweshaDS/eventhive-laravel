<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerController extends Controller
{
    private function authorizeOrganizer()
    {
        if (!auth()->check() || !auth()->user()->isOrganizer()) {
            abort(403, 'Unauthorized.');
        }
    }

    public function dashboard()
    {
        $this->authorizeOrganizer();
        $organizer_id = Auth::id();

        $stats = [
            'total_events'  => Event::where('organizer_id', $organizer_id)->count(),
            'live_events'   => Event::where('organizer_id', $organizer_id)
                                ->where('status', 'published')
                                ->where('event_date', '>=', now())->count(),
            'total_bookings'=> Booking::whereHas('ticketType.event', function ($q) use ($organizer_id) {
                                $q->where('organizer_id', $organizer_id);
                              })->where('booking_status', 'confirmed')->count(),
            'total_revenue' => Booking::whereHas('ticketType.event', function ($q) use ($organizer_id) {
                                $q->where('organizer_id', $organizer_id);
                              })->where('payment_status', 'paid')->sum('total_amount'),
        ];

        $recent_events = Event::with('ticketTypes')
            ->where('organizer_id', $organizer_id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('organizer.dashboard', compact('stats', 'recent_events'));
    }

    public function events()
    {
        $this->authorizeOrganizer();

        $events = Event::with('ticketTypes')
            ->where('organizer_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('organizer.events', compact('events'));
    }

    public function create()
    {
        $this->authorizeOrganizer();
        return view('organizer.create');
    }

    public function store(Request $request)
    {
        $this->authorizeOrganizer();

        $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string',
            'category'    => 'required|string',
            'venue'       => 'required|string',
            'city'        => 'required|string',
            'event_date'  => 'required|date',
        ]);

        $banner_image = null;
        if ($request->hasFile('banner')) {
            $file         = $request->file('banner');
            $banner_image = 'event_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $banner_image);
        }

        $event = Event::create([
            'organizer_id' => Auth::id(),
            'title'        => $request->title,
            'description'  => $request->description,
            'category'     => $request->category,
            'venue'        => $request->venue,
            'city'         => $request->city,
            'event_date'   => $request->event_date,
            'event_end'    => $request->event_end,
            'banner_image' => $banner_image,
            'status'       => $request->status ?? 'draft',
        ]);

        $this->saveTicketTypes($request, $event->id);

        return redirect()->route('organizer.events')
                         ->with('success', 'Event created successfully!');
    }

    public function edit($id)
    {
        $this->authorizeOrganizer();

        $event   = Event::with('ticketTypes')
                        ->where('organizer_id', Auth::id())
                        ->findOrFail($id);

        return view('organizer.create', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeOrganizer();

        $event = Event::where('organizer_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string',
            'category'    => 'required|string',
            'venue'       => 'required|string',
            'city'        => 'required|string',
            'event_date'  => 'required|date',
        ]);

        $banner_image = $event->banner_image;
        if ($request->hasFile('banner')) {
            $file         = $request->file('banner');
            $banner_image = 'event_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $banner_image);
        }

        $event->update([
            'title'        => $request->title,
            'description'  => $request->description,
            'category'     => $request->category,
            'venue'        => $request->venue,
            'city'         => $request->city,
            'event_date'   => $request->event_date,
            'event_end'    => $request->event_end,
            'banner_image' => $banner_image,
            'status'       => $request->status ?? 'draft',
        ]);

        $event->ticketTypes()->delete();
        $this->saveTicketTypes($request, $event->id);

        return redirect()->route('organizer.events')
                         ->with('success', 'Event updated successfully!');
    }

    public function destroy($id)
    {
        $this->authorizeOrganizer();
        Event::where('organizer_id', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Event deleted successfully.');
    }

    public function attendees(Request $request)
    {
        $this->authorizeOrganizer();

        $event_filter = (int)($request->get('event_id', 0));
        $my_events    = Event::where('organizer_id', Auth::id())
                             ->orderByDesc('event_date')->get();

        $query = Booking::with(['user', 'ticketType.event', 'qrTicket'])
            ->whereHas('ticketType.event', function ($q) {
                $q->where('organizer_id', Auth::id());
            })
            ->where('booking_status', 'confirmed');

        if ($event_filter) {
            $query->whereHas('ticketType', function ($q) use ($event_filter) {
                $q->where('event_id', $event_filter);
            });
        }

        $attendees = $query->orderByDesc('created_at')->get();

        return view('organizer.attendees', compact('attendees', 'my_events', 'event_filter'));
    }

    private function saveTicketTypes(Request $request, $event_id)
    {
        $names  = $request->input('ticket_name', []);
        $prices = $request->input('ticket_price', []);
        $seats  = $request->input('ticket_seats', []);

        foreach ($names as $i => $name) {
            $name = trim($name);
            if ($name && isset($seats[$i]) && $seats[$i] > 0) {
                TicketType::create([
                    'event_id'    => $event_id,
                    'name'        => $name,
                    'price'       => (float)($prices[$i] ?? 0),
                    'total_seats' => (int)$seats[$i],
                ]);
            }
        }
    }
}