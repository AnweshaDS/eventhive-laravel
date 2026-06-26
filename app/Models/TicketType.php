<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    protected $fillable = [
        'event_id', 'name', 'price', 'total_seats', 'booked_seats'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getSeatsLeftAttribute()
    {
        return $this->total_seats - $this->booked_seats;
    }

    public function isSoldOut(): bool
    {
        return $this->seats_left <= 0;
    }
}