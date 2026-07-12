<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\QrTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with([
                'ticketType.event',
                'qrTicket'
            ])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id'       => 'required|integer',
            'ticket_type_id' => 'required|integer',
            'quantity'       => 'required|integer|min:1',
        ]);

        $event = Event::where('status', 'published')
                      ->findOrFail($request->event_id);

        $ticket = TicketType::where('event_id', $event->id)
                            ->findOrFail($request->ticket_type_id);

        $seats_left = $ticket->total_seats - $ticket->booked_seats;
        if ($seats_left < $request->quantity) {
            return back()->with('error', 'Sorry, not enough seats available.');
        }

        $already = Booking::where('user_id', Auth::id())
                          ->where('ticket_type_id', $ticket->id)
                          ->where('booking_status', 'confirmed')
                          ->exists();

        if ($already) {
            return back()->with('error', 'You have already booked this ticket type.');
        }

        $total_amount = $ticket->price * $request->quantity;

        try {
            \DB::beginTransaction();

            $booking = Booking::create([
                'user_id'        => Auth::id(),
                'ticket_type_id' => $ticket->id,
                'quantity'       => $request->quantity,
                'total_amount'   => $total_amount,
                'payment_status' => 'paid',
                'booking_status' => 'confirmed',
            ]);

            $ticket->increment('booked_seats', $request->quantity);

            $verify_token = strtoupper(bin2hex(random_bytes(8)));

            $qr_content = json_encode([
                'booking_id'   => $booking->id,
                'event'        => $event->title,
                'ticket'       => $ticket->name,
                'quantity'     => $request->quantity,
                'attendee_id'  => Auth::id(),
                'verify_token' => $verify_token,
                'verify_url'   => url('/qr/verify?token=' . $verify_token),
            ]);

            $qr_dir      = public_path('images/qrcodes/');
            $qr_filename = 'qr_' . $booking->id . '_' . $verify_token . '.png';
            $qr_path     = $qr_dir . $qr_filename;

            if (!is_dir($qr_dir)) {
                mkdir($qr_dir, 0755, true);
            }

            $qrCode = new QrCode($qr_content);
            $writer  = new PngWriter();
            $result  = $writer->write($qrCode);
            $result->saveToFile($qr_path);

            QrTicket::create([
                'booking_id'   => $booking->id,
                'qr_code'      => $qr_filename,
                'verify_token' => $verify_token,
                'scan_status'  => 'unused',
            ]);

            \DB::commit();

            return redirect()->route('bookings.index')
                             ->with('success', 'Booking confirmed! Your QR ticket is ready.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Booking failed. Please try again.');
        }
    }

    public function cancel($id)
    {
        $booking = Booking::where('user_id', Auth::id())
                      ->where('booking_status', 'confirmed')
                      ->findOrFail($id);

        // Get event date
        $event_date = $booking->ticketType->event->event_date;
        $days_left  = now()->diffInDays($event_date, false);

        // Must be 7+ days before event
        if ($days_left < 7) {
            return back()->with('error',
                'Cancellation is only allowed 7 or more days before the event. ' .
                'This event is ' . max(0, $days_left) . ' day(s) away.'
            );
        }
    

        try {
            \DB::beginTransaction();

            // Release seats back
            $booking->ticketType->decrement('booked_seats', $booking->quantity);

            // Update booking status
            $booking->update([
                'booking_status' => 'cancelled',
                'payment_status' => $booking->total_amount > 0 ? 'refunded' : 'pending',
            ]);

            \DB::commit();

            $message = $booking->total_amount > 0
            ? 'Booking cancelled successfully. Your refund of ৳' . number_format($booking->total_amount, 0) . ' will be processed within 3-5 business days.'
            : 'Booking cancelled successfully.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Cancellation failed. Please try again.');
        }
    }
}
