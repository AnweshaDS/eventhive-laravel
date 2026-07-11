<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected WeatherService $weather;

    public function __construct(WeatherService $weather)
    {
        $this->weather = $weather;
    }

    public function index(Request $request)
    {
        $search   = $request->get('search', '');
        $category = $request->get('category', '');
        $city     = $request->get('city', '');
        $sort     = $request->get('sort', 'date');

        $query = Event::with(['organizer', 'ticketTypes'])
            ->where('status', 'published')
            ->where('event_date', '>=', now());

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('venue', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%");
            });
        }

        if ($category) $query->where('category', $category);
        if ($city)     $query->where('city', 'like', "%$city%");

        $query = match($sort) {
            'popular' => $query->orderByDesc('id'),
            default   => $query->orderBy('event_date'),
        };

        $events = $query->get();
        $cities = Event::where('status', 'published')->distinct()->pluck('city');

        $icons = [
            'Concert'    => 'fa-music',
            'Workshop'   => 'fa-screwdriver-wrench',
            'Conference' => 'fa-briefcase',
            'Food'       => 'fa-utensils',
            'Sports'     => 'fa-futbol',
            'Comedy'     => 'fa-face-laugh',
            'Tech'       => 'fa-laptop-code',
            'Art'        => 'fa-palette',
        ];

        return view('events.index', compact('events', 'cities', 'icons', 'search', 'category', 'city', 'sort'));
    }

    public function show($id)
    {
        $event = Event::with(['organizer', 'ticketTypes', 'reviews.user'])
            ->where('status', 'published')
            ->findOrFail($id);

        $icons = [
            'Concert'    => 'fa-music',
            'Workshop'   => 'fa-screwdriver-wrench',
            'Conference' => 'fa-briefcase',
            'Food'       => 'fa-utensils',
            'Sports'     => 'fa-futbol',
            'Comedy'     => 'fa-face-laugh',
            'Tech'       => 'fa-laptop-code',
            'Art'        => 'fa-palette',
        ];

        $avg_rating = $event->reviews->avg('rating');

        // Guzzle - fetch weather
        $weather = null;
        $daysDiff = now()->diffInDays($event->event_date, false);
        if ($daysDiff >= 0 && $daysDiff <= 5) {
            $weather = $this->weather->getForecast(
                $event->city,
                $event->event_date->format('Y-m-d H:i:s')
            );
        } elseif ($daysDiff < 0) {
            $weather = $this->weather->getCurrentWeather($event->city);
        }

        return view('events.show', compact('event', 'icons', 'avg_rating', 'weather'));
    }
}