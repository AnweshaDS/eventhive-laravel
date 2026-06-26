<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id', 'title', 'description', 'category',
        'venue', 'city', 'event_date', 'event_end',
        'banner_image', 'status'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_end'  => 'datetime',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getTotalSeatsAttribute()
    {
        return $this->ticketTypes->sum('total_seats');
    }

    public function getAvailableSeatsAttribute()
    {
        return $this->ticketTypes->sum(fn($t) => $t->total_seats - $t->booked_seats);
    }

    public function getMinPriceAttribute()
    {
        return $this->ticketTypes->min('price');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews->avg('rating');
    }
}