<?php

namespace App\Http\Controllers;

use App\Models\Reservation; 
use App\Mail\BookingReceipt; 
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class BookingController extends Controller
{
    public function kioskReserve(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        $bookedSlots = Reservation::where('reservation_date', $date)
                        ->orderBy('start_time', 'asc')
                        ->get();
        
        $global_discount = Cache::get('global_discount', 0); 
        $maxCapacity = 10; 
        $isFull = $bookedSlots->count() >= $maxCapacity;
        
        return view('bookings.reserve', [
            'date' => $date,
            'bookedSlots' => $bookedSlots,
            'isAdmin' => false,
            'global_discount' => $global_discount,
            'isFull' => $isFull
        ]);
    }

    public function adminReserve(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        $bookedSlots = Reservation::where('reservation_date', $date)
                        ->orderBy('start_time', 'asc') 
                        ->get();
        
        $global_discount = Cache::get('global_discount', 0); 
        $maxCapacity = 10;
        $isFull = $bookedSlots->count() >= $maxCapacity;
        
        // If it's an AJAX request from the calendar, return JSON
        if ($request->ajax()) {
            return response()->json(['bookedSlots' => $bookedSlots]);
        }

        return view('bookings.reserve', [
            'date' => $date,
            'bookedSlots' => $bookedSlots,
            'isAdmin' => true,
            'global_discount' => $global_discount,
            'isFull' => $isFull
        ]);
    }

    public function rescheduleFromEmail($id)
    {
        $booking = Reservation::findOrFail($id);

        return redirect()->route('reserve.index')->with([
            'reschedule_id' => $booking->id,
            'prefill_name'  => $booking->player_name,
            'prefill_email' => $booking->email,
            'prefill_duration'  => $booking->duration,
            'prefill_paddle'    => $booking->paddle_rental,
            'prefill_payment'   => $booking->payment_method,
            'prefill_equipment' => $booking->rent_equipment,
        ]);
    }

    // --- UPDATED SEND OTP (Aligned with web.php /otp/send) ---
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $otp = rand(100000, 999999);
        // Store in cache for 15 minutes
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(15));
        
        try {
            Mail::raw("Your Pickleball Kiosk verification code is: $otp", function($m) use ($request){ 
                $m->to($request->email)->subject('Verification Code'); 
            });
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('OTP Send Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send email. Check your .env settings.'
            ], 500);
        }
    }

    // --- UPDATED VERIFY OTP (Aligned with web.php /otp/verify) ---
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        if (Cache::get('otp_' . $request->email) == $request->otp) {
            // Mark email as verified for 30 minutes
            Cache::put('verified_' . $request->email, true, now()->addMinutes(30));
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false, 
            'message' => 'Invalid or expired OTP.'
        ], 422);
    }

    public function store(Request $request)
    {
        $request->validate([
            'player_name' => 'required|string',
            'email' => ['nullable', 'email', 'regex:/^[\w\-\.]+@gmail\.com$/i'],
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|integer', 
            'payment_method' => 'required|string',
            'paddle_rental' => 'nullable|string', 
            'rent_equipment' => 'nullable', 
            'reference_number' => 'nullable|string',
            'proof_of_payment' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', 
        ]);

        // Only enforce verification for non-admin kiosk users
        if (!$request->has('isAdmin') && $request->filled('email')) {
            if (!Cache::get('verified_' . $request->email)) {
                return back()->with('error', 'You must verify your email address before booking.');
            }
        }

        try {
            $start = Carbon::parse($request->start_time);
            $duration = (int)$request->duration;
            $end = $start->copy()->addHours($duration);
        } catch (\Exception $e) {
            return back()->with('error', 'Invalid start time selected.');
        }

        $conflictQuery = Reservation::where('reservation_date', $request->reservation_date)
            ->where(function($query) use ($start, $end) {
                $query->where('start_time', '<', $end->format('H:i'))
                      ->where('end_time', '>', $start->format('H:i'));
            });

        if ($request->filled('booking_id')) {
            $conflictQuery->where('id', '!=', $request->booking_id);
        }

        if ($conflictQuery->exists()) {
            return back()->with('error', 'That time slot overlaps with an existing booking.');
        }

        $paddleRentalString = $request->input('paddle_rental') ?? $request->input('rent_equipment') ?? 'None';
        $equipmentFee = $this->extractRentalPrice($paddleRentalString);

        $hourlyRate = 200; 
        $totalPrice = ($hourlyRate * $duration) + $equipmentFee;

        $proofPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofPath = $request->file('proof_of_payment')->store('receipts', 'public');
        }

        if ($request->filled('booking_id')) {
            $reservation = Reservation::findOrFail($request->booking_id);
            $reservation->update([
                'reservation_date' => $request->reservation_date,
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'duration' => $duration,
                'price' => $totalPrice,
                'paddle_rental' => $paddleRentalString, 
                'rent_equipment' => $equipmentFee,
            ]);
            $successMsg = 'Reschedule successful!';
        } else {
            $reservation = Reservation::create([
                'player_name' => $request->player_name,
                'email' => $request->email, 
                'reservation_date' => $request->reservation_date,
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'), // Fixed: Added end_time here
                'duration' => $duration,
                'price' => $totalPrice, 
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'reference_number' => $request->reference_number,
                'proof_of_payment' => $proofPath,
                'paddle_rental' => $paddleRentalString, 
                'rent_equipment' => $equipmentFee,
            ]);
            $successMsg = 'Booking confirmed for ₱' . $totalPrice;
        }

        if ($request->filled('email')) {
            try {
                $rescheduleUrl = URL::temporarySignedRoute(
                    'reserve.reschedule', 
                    now()->addDays(3), 
                    ['id' => $reservation->id]
                );

                Mail::to($reservation->email)->send(new BookingReceipt($reservation, $rescheduleUrl));
            } catch (\Exception $e) { 
                Log::error('Mail Error: ' . $e->getMessage()); 
            }
        }

        return ($request->has('isAdmin') || $request->is('admin-panel/*') ? back() : redirect()->route('booking.success'))->with('success', $successMsg);
    }

    private function extractRentalPrice($rentalString)
    {
        if (empty($rentalString) || $rentalString === 'None' || $rentalString === '0') {
            return 0;
        }

        if (strpos($rentalString, '400') !== false) return 400;
        if (strpos($rentalString, '300') !== false) return 300;
        if (strpos($rentalString, '200') !== false) return 200;
        if (strpos($rentalString, '100') !== false) return 100;

        return is_numeric($rentalString) ? (float)$rentalString : 0;
    }

    public function printSummary(Request $request)
    {
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $query = Reservation::query();

        if ($fromDate && $toDate) {
            $query->whereBetween('reservation_date', [$fromDate, $toDate]);
            $period = Carbon::parse($fromDate)->format('M d, Y') . ' to ' . Carbon::parse($toDate)->format('M d, Y');
        } else {
            $today = date('Y-m-d');
            $query->where('reservation_date', $today);
            $period = Carbon::parse($today)->format('M d, Y');
        }

        $bookings = $query->orderBy('reservation_date', 'asc')->orderBy('start_time', 'asc')->get();
        return view('admin.print_summary', compact('bookings', 'period'));
    }

    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking for ' . $reservation->player_name . ' has been cancelled.');
    }

    public function approve($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'approved']);
        return back()->with('success', 'Booking for ' . $reservation->player_name . ' has been approved.');
    }
}