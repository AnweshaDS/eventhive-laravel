<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['organizer:id,name', 'ticketTypes'])
            ->where('status', 'published')
            ->where('event_date', '>=', now());

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('city') && $request->city) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        $events = $query->orderBy('event_date')->get()->map(function ($event) {
            return [
                'id'             => $event->id,
                'title'          => $event->title,
                'description'    => $event->description,
                'category'       => $event->category,
                'venue'          => $event->venue,
                'city'           => $event->city,
                'event_date'     => $event->event_date->format('Y-m-d H:i:s'),
                'event_date_human' => $event->event_date->format('D, M j Y · g:i A'),
                'organizer'      => $event->organizer->name,
                'min_price'      => $event->min_price,
                'available_seats'=> $event->available_seats,
                'banner_image'   => $event->banner_image,
                'status'         => $event->status,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $events->count(),
            'data'    => $events,
        ]);
    }

    public function show($id)
    {
        $event = Event::with(['organizer:id,name', 'ticketTypes', 'reviews.user:id,name'])
            ->where('status', 'published')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $event->id,
                'title'           => $event->title,
                'description'     => $event->description,
                'category'        => $event->category,
                'venue'           => $event->venue,
                'city'            => $event->city,
                'event_date'      => $event->event_date->format('Y-m-d H:i:s'),
                'event_date_human'=> $event->event_date->format('D, M j Y · g:i A'),
                'event_end'       => $event->event_end?->format('g:i A'),
                'organizer'       => $event->organizer->name,
                'min_price'       => $event->min_price,
                'available_seats' => $event->available_seats,
                'average_rating'  => round($event->reviews->avg('rating'), 1),
                'total_reviews'   => $event->reviews->count(),
                'ticket_types'    => $event->ticketTypes->map(fn($t) => [
                    'id'          => $t->id,
                    'name'        => $t->name,
                    'price'       => $t->price,
                    'seats_left'  => $t->seats_left,
                    'is_sold_out' => $t->isSoldOut(),
                ]),
            ],
        ]);
    }

    public function categories()
    {
        $categories = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->select('category')
            ->distinct()
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'live_events'  => Event::where('status', 'published')->where('event_date', '>=', now())->count(),
                'total_events' => Event::count(),
                'cities'       => Event::where('status', 'published')->distinct()->pluck('city'),
                'categories'   => Event::where('status', 'published')->distinct()->pluck('category'),
            ],
        ]);
    }
}