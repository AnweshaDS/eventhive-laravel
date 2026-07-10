<?php

namespace App\Http\Controllers;

use App\Models\QrTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrController extends Controller
{
    public function verify(Request $request)
    {
        $token   = $request->get('token', '');
        $booking = null;
        $error   = '';

        if (!$token) {
            $error = 'No verification token provided.';
        } else {
            $qr = QrTicket::with([
                'booking.user',
                'booking.ticketType.event'
            ])->where('verify_token', $token)->first();

            if (!$qr) {
                $error = 'Invalid token. This ticket does not exist.';
            } elseif ($qr->booking->booking_status !== 'confirmed') {
                $error = 'This booking has been cancelled.';
            } else {
                $booking = $qr;
            }
        }

        return view('qr.verify', compact('booking', 'error', 'token'));
    }

    public function markScanned(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'organizer') {
            return redirect()->route('home');
        }

        $token = $request->get('token', '');
        if ($token) {
            QrTicket::where('verify_token', $token)
                    ->update(['scan_status' => 'scanned']);
        }

        return redirect()->route('qr.verify', ['token' => $token]);
    }
}