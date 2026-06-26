<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrTicket extends Model
{
    protected $fillable = [
        'booking_id', 'qr_code', 'verify_token', 'scan_status'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}