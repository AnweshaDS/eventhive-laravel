<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->authorizeAdmin();

        $stats = [
            'total_users'     => User::count(),
            'total_events'    => Event::count(),
            'total_bookings'  => Booking::where('booking_status', 'confirmed')->count(),
            'total_revenue'   => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'live_events'     => Event::where('status', 'published')->where('event_date', '>=', now())->count(),
            'total_organizers'=> User::where('role', 'organizer')->count(),
        ];

        $recent_bookings = Booking::with(['user', 'ticketType.event'])
            ->where('booking_status', 'confirmed')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $recent_events = Event::with(['organizer', 'ticketTypes'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'recent_events'));
    }

    public function users(Request $request)
    {
        $this->authorizeAdmin();

        $search = $request->get('search', '');
        $filter = $request->get('role', '');

        $query = User::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        if ($filter) {
            $query->where('role', $filter);
        }

        $users = $query->orderByDesc('created_at')->get();
        return view('admin.users', compact('users', 'search', 'filter'));
    }

    public function changeRole(Request $request)
    {
        $this->authorizeAdmin();
        $request->validate(['user_id' => 'required|integer', 'new_role' => 'required|in:attendee,organizer,admin']);
        User::findOrFail($request->user_id)->update(['role' => $request->new_role]);
        return back()->with('success', 'Role updated successfully.');
    }

    public function deleteUser($id)
    {
        $this->authorizeAdmin();
        if ($id != auth()->id()) {
            User::findOrFail($id)->delete();
        }
        return back()->with('success', 'User deleted.');
    }

    public function events(Request $request)
    {
        $this->authorizeAdmin();

        $search = $request->get('search', '');
        $filter = $request->get('status', '');

        $query = Event::with(['organizer', 'ticketTypes']);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%");
            });
        }
        if ($filter) {
            $query->where('status', $filter);
        }

        $events = $query->orderByDesc('created_at')->get();
        return view('admin.events', compact('events', 'search', 'filter'));
    }

    public function changeStatus(Request $request)
    {
        $this->authorizeAdmin();
        $request->validate(['event_id' => 'required|integer', 'new_status' => 'required|in:published,draft,cancelled']);
        Event::findOrFail($request->event_id)->update(['status' => $request->new_status]);
        return back()->with('success', 'Event status updated.');
    }

    public function deleteEvent($id)
    {
        $this->authorizeAdmin();
        Event::findOrFail($id)->delete();
        return back()->with('success', 'Event deleted.');
    }

    private function authorizeAdmin()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }
    }
}