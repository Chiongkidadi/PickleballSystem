<?php

namespace App\Http\Controllers;

use App\Models\Reservation; 
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    /**
     * KIOSK VIEW: Show the Reservation Calendar & Form (No Dashboard Button)
     */
    public function kioskReserve(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        
        // Fetch booked slots for the selected date to display on the calendar view
        $bookedSlots = Reservation::where('reservation_date', $date)->get();
        
        return view('bookings.reserve', [
            'date' => $date,
            'bookedSlots' => $bookedSlots,
            'isAdmin' => false // Hides the "Back to Dashboard" button
        ]);
    }

    /**
     * ADMIN VIEW: Show the Reservation Calendar & Form (WITH Dashboard Button)
     */
    public function adminReserve(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        
        // Fetch booked slots for the selected date to display on the calendar view
        $bookedSlots = Reservation::where('reservation_date', $date)->get();
        
        return view('bookings.reserve', [
            'date' => $date,
            'bookedSlots' => $bookedSlots,
            'isAdmin' => true // Shows the "Back to Dashboard" button
        ]);
    }

    /**
     * Handle the form submission (Used by both Kiosk and Admin views)
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'player_name' => 'required|string',
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|string', 
            'payment_method' => 'required|string', // Validates Cash/Online
        ]);

        // Extracts the number (e.g., "2") from strings like "2 Hours"
        $durationValue = (int) filter_var($request->duration, FILTER_SANITIZE_NUMBER_INT);

        // Calculate exact start and end times
        $start = Carbon::parse($request->start_time);
        $end = $start->copy()->addHours($durationValue);

        // 2. CHECK FOR DOUBLE BOOKING (Prevents overlapping times)
        $conflict = Reservation::where('reservation_date', $request->reservation_date)
            ->where(function($query) use ($start, $end) {
                // If a booking exists where its start time is before our new end time, 
                // AND its end time is after our new start time, it's a conflict!
                $query->where('start_time', '<', $end->format('H:i'))
                      ->where('end_time', '>', $start->format('H:i'));
            })->exists();

        // If a conflict exists, stop and return an error message
        if ($conflict) {
            return back()->with('error', 'That time slot overlaps with an existing booking. Please choose another time.');
        }

        // 3. CALCULATE EXACT PRICE (Matches frontend JS logic)
        $hour = (int) $start->format('H');
        $hourlyRate = ($hour < 12) ? 150 : 200; // 150 before 12 PM, 200 from 12 PM onwards
        $totalPrice = $hourlyRate * $durationValue;

        // Add ₱100 equipment fee if they checked the box
        if ($request->has('rent_equipment')) {
            $totalPrice += 100;
        }

        // 4. Save the new booking to the database
        Reservation::create([
            'player_name' => $request->player_name,
            'reservation_date' => $request->reservation_date,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'duration' => $durationValue,
            'price' => $totalPrice, // Saves dynamic price to the database
            'payment_method' => $request->payment_method, // Saves "Cash" or "Online"
            'status' => 'pending',
        ]);

        return back()->with('success', 'Booking confirmed!');
    }
}