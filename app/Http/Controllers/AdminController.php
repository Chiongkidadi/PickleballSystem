<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $promo_mode = Cache::get('promo_mode', 'full');
        $global_discount = Cache::get('global_discount', 0);

        // --- 1. BASE QUERY (Applies to everything: Dashboard stats AND Table) ---
        $baseQuery = Reservation::query();

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('reservation_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('reservation_date', '<=', $request->end_date);
        }

        // Quick-filters fallback (Day/Month/Year) if no explicit date is set
        $reportType = $request->get('report', 'all');
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            if ($reportType == 'day') {
                $baseQuery->whereDate('reservation_date', Carbon::today());
            } elseif ($reportType == 'month') {
                $baseQuery->whereMonth('reservation_date', Carbon::now()->month)
                          ->whereYear('reservation_date', Carbon::now()->year);
            } elseif ($reportType == 'year') {
                $baseQuery->whereYear('reservation_date', Carbon::now()->year);
            }
        }

        // --- 2. ANALYTICS (Calculated BEFORE player/status filters are applied) ---
        // This ensures your Dashboard top widgets always show the real totals for the selected dates.
        $total = (clone $baseQuery)->count();
        $paid = (clone $baseQuery)->whereIn('status', ['paid', 'approved'])->count();
        $pending = (clone $baseQuery)->where('status', 'pending')->count();
        $revenue = (clone $baseQuery)->whereIn('status', ['paid', 'approved'])->sum('price');

        // --- 3. TABLE FILTERS & SEARCH (Applies ONLY to the Bookings list) ---
        $tableQuery = clone $baseQuery;

        if ($request->filled('search_name')) {
            $tableQuery->where('player_name', 'like', '%' . $request->search_name . '%');
        }

        if ($request->filled('filter_status')) {
            // strtolower prevents issues if the DB has "Paid" but the form sends "paid"
            $tableQuery->where('status', strtolower($request->filter_status));
        }

        if ($request->filled('filter_mop')) {
            // Using 'LIKE' instead of strict '=' bypasses uppercase/lowercase mismatch issues (e.g. CASH vs Cash)
            $tableQuery->where('payment_method', 'like', $request->filter_mop);
        }

        // --- 4. DATA & PAGINATION ---
        $reservations = $tableQuery->orderBy('reservation_date', 'desc')
                                   ->orderBy('start_time', 'desc')
                                   ->paginate(10);

        // Chart Data (Mocked - replace with actual DB queries if needed)
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $counts = [5, 10, 15, 7, 20, 25, 30]; 

        return view('admin.dashboard', compact(
            'reservations', 'total', 'paid', 'pending', 'revenue', 'days', 'counts', 'promo_mode', 'global_discount'
        ));
    }

    public function printSummary(Request $request)
    {
        $query = Reservation::query();
        if ($request->filled('start_date')) $query->whereDate('reservation_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('reservation_date', '<=', $request->end_date);

        $bookings = $query->orderBy('reservation_date', 'asc')->get();
        $totalBookings = $bookings->count();
        $totalRevenue = $bookings->sum('price');

        return view('admin.print_summary', compact('bookings', 'totalBookings', 'totalRevenue'));
    }

    public function updatePromo(Request $request)
    {
        $request->validate(['global_discount' => 'required|numeric|min:0|max:100']);
        Cache::forever('global_discount', $request->global_discount);
        return back()->with('success', 'Discount updated successfully!');
    }

    public function approveReservation($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'approved']);
        return back()->with('success', 'Booking approved!');
    }
}