<?php

namespace App\Http\Controllers;

use App\Models\Reservation; 
use App\Mail\BookingReceipt; 
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * KIOSK VIEW: Show the Reservation Calendar & Form
     */
    public function kioskReserve(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        $bookedSlots = Reservation::where('reservation_date', $date)
                        ->orderBy('start_time', 'asc')
                        ->get();
        
        $global_discount = Cache::get('global_discount', 0); 

        // Check if full (Example: 10 slots max per day)
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

    /**
     * ADMIN VIEW: Show the Reservation Calendar & Form
     */
    public function adminReserve(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        
        // FIXED: Now orders by start_time ascending instead of created_at descending
        $bookedSlots = Reservation::where('reservation_date', $date)
                        ->orderBy('start_time', 'asc') 
                        ->get();
        
        $global_discount = Cache::get('global_discount', 0); 

        // Check if full
        $maxCapacity = 10;
        $isFull = $bookedSlots->count() >= $maxCapacity;
        
        return view('bookings.reserve', [
            'date' => $date,
            'bookedSlots' => $bookedSlots,
            'isAdmin' => true,
            'global_discount' => $global_discount,
            'isFull' => $isFull
        ]);
    }

    /**
     * Handle the form submission and send the Receipt
     */
    public function store(Request $request)
    {
        // 1. VALIDATION
        $request->validate([
            'player_name' => 'required|string',
            'email' => [
                'nullable', 
                'email', 
                'regex:/^[\w\-\.]+@gmail\.com$/i' 
            ],
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|integer', 
            'payment_method' => 'required|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100', 
            'rent_equipment' => 'nullable|integer',
        ], [
            'email.regex' => 'Email not recognized. Please use a valid @gmail.com address.',
            'email.email' => 'Please enter a valid email format.'
        ]);

        // ==========================================
        // NEW SECURITY: Prevent booking if email is not verified
        // (Bypassed if submitted by admin)
        // ==========================================
        if (!$request->has('isAdmin') && $request->filled('email')) {
            $isVerified = Cache::get('verified_' . $request->email);
            if (!$isVerified) {
                return back()->with('error', 'You must verify your email address before booking.');
            }
        }
        // ==========================================

        $durationValue = (int) $request->duration;
        $start = Carbon::parse($request->start_time);
        $end = $start->copy()->addHours($durationValue);

        // EXTRA SECURITY: Final check for capacity before saving
        $maxCapacity = 10;
        $currentBookings = Reservation::where('reservation_date', $request->reservation_date)->count();
        if ($currentBookings >= $maxCapacity) {
             return back()->with('error', 'Sorry, this day just became fully booked.');
        }

        // 2. CHECK FOR DOUBLE BOOKING
        $conflict = Reservation::where('reservation_date', $request->reservation_date)
            ->where(function($query) use ($start, $end) {
                $query->where('start_time', '<', $end->format('H:i'))
                      ->where('end_time', '>', $start->format('H:i'));
            })->exists();

        if ($conflict) {
            return back()->with('error', 'That time slot overlaps with an existing booking.');
        }

        // 3. CALCULATE PRICING
        $hour = (int) $start->format('H');
        $baseRate = ($hour < 12) ? 300 : 400; 
        $totalPrice = $baseRate * $durationValue;

        if ($request->filled('discount_percent')) {
            $percent = (float) $request->discount_percent;
            $totalPrice -= ($percent / 100) * $totalPrice;
        } else {
            $global_discount = Cache::get('global_discount', 0);
            if ($global_discount > 0) {
                $totalPrice -= ($global_discount / 100) * $totalPrice;
            }
        }

        $equipmentFee = (int) ($request->rent_equipment ?? 0);
        $totalPrice += $equipmentFee;

        // 4. SAVE TO DATABASE
        $reservation = Reservation::create([
            'player_name' => $request->player_name,
            'email' => $request->email, 
            'reservation_date' => $request->reservation_date,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'), 
            'duration' => $durationValue,
            'price' => round($totalPrice, 2), 
            'payment_method' => $request->payment_method,
            'rent_equipment' => $equipmentFee,
            'status' => 'pending',
        ]);

        // 5. SEND EMAIL RECEIPT
        if ($request->filled('email')) {
            try {
                Mail::to($request->email)->send(new BookingReceipt($reservation));
                return back()->with('success', 'Booking confirmed! A receipt has been sent to ' . $request->email);
            } catch (\Exception $e) {
                Log::error('Mail Error: ' . $e->getMessage());
                return back()->with('success', 'Booking confirmed! (Note: The receipt email failed to send).');
            }
        }

        return back()->with('success', 'Booking confirmed!');
    }

    /**
     * PRINT SUMMARY: Generates a printable report of filtered bookings
     */
    public function printSummary(Request $request) 
    {
        // 1. Initialize query
        $query = Reservation::query();

        // 2. Apply Date Filters from the URL
        if ($request->filled('start_date')) {
            $query->whereDate('reservation_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('reservation_date', '<=', $request->end_date);
        }

        // 3. Fetch filtered bookings
        $bookings = $query->orderBy('reservation_date', 'desc')
                          ->orderBy('start_time', 'asc')
                          ->get();

        // 4. Calculate stats ONLY for filtered bookings
        $totalRevenue = $bookings->filter(function($b) {
            return in_array(strtolower($b->status), ['paid', 'approved']);
        })->sum('price');

        $totalBookings = $bookings->count();

        // 5. Return view with date range info for the report header
        return view('admin.print_summary', [
            'bookings' => $bookings,
            'totalRevenue' => $totalRevenue,
            'totalBookings' => $totalBookings,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date
        ]);
    }

    // ==========================================
    // NEW METHODS: OTP VERIFICATION
    // ==========================================

    /**
     * Send a 6-digit OTP to the user's email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|regex:/^[\w\-\.]+@gmail\.com$/i'
        ]);

        $otp = rand(100000, 999999);
        
        // Save the OTP in the cache for 10 minutes
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(10));

        // Using Mail::raw so you don't have to create a new Blade email view
        try {
            Mail::raw("Your Pickleball Reservation verification code is: $otp", function ($message) use ($request) {
                $message->to($request->email)->subject('Your Verification Code');
            });
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('OTP Mail Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send email. Check mail settings.']);
        }
    }

    /**
     * Verify the code matches what we saved
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|numeric']);
        
        $cachedOtp = Cache::get('otp_' . $request->email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            // Mark as verified in the cache for 30 minutes
            Cache::put('verified_' . $request->email, true, now()->addMinutes(30));
            // Clear the OTP so it can't be reused
            Cache::forget('otp_' . $request->email);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
    }
}