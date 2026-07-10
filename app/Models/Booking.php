<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'ticket_type_id', 'quantity',
        'total_amount', 'payment_status', 'booking_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function qrTicket()
    {
        return $this->hasOne(QrTicket::class);
    }
}