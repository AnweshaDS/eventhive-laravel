<?php

namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $events = Event::with(['organizer', 'ticketTypes'])
            ->where('status', 'published')
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->take(6)
            ->get();

        $stats = [
            'live_events'  => Event::where('status', 'published')
                                ->where('event_date', '>=', now())
                                ->count(),
            'organizers'   => User::where('role', 'organizer')->count(),
            'tickets_sold' => Booking::where('booking_status', 'confirmed')->count(),
        ];
        return view('home', compact('events', 'stats'));
    }
}