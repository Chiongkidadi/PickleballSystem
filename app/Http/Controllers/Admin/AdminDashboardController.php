<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $promo_mode = Cache::get('promo_mode', 'full');
        $global_discount = Cache::get('global_discount', 0);
        
        // Added: Grab the active tab from the URL so the page stays on "Booked Slots" after filtering
        $active_tab = $request->get('active_tab', 'dashboard');

        // 1. Start with a Base Query for filtering
        $baseQuery = Reservation::query();

        // Apply Date Range (Global for both stats and table)
        if ($request->filled('start_date')) {
            $baseQuery->whereDate('reservation_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('reservation_date', '<=', $request->end_date);
        }

        // Fallback to today/month/year if no specific dates are picked
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $reportType = $request->get('report', 'all');
            if ($reportType == 'day') {
                $baseQuery->whereDate('reservation_date', Carbon::today());
            } elseif ($reportType == 'month') {
                $baseQuery->whereMonth('reservation_date', Carbon::now()->month)
                          ->whereYear('reservation_date', Carbon::now()->year);
            } elseif ($reportType == 'year') {
                $baseQuery->whereYear('reservation_date', Carbon::now()->year);
            }
        }

        // 2. ANALYTICS (Calculated based on dates, but BEFORE player search)
        $total = (clone $baseQuery)->count();
        $paid = (clone $baseQuery)->whereIn('status', ['paid', 'approved'])->count();
        $pending = (clone $baseQuery)->where('status', 'pending')->count();
        $revenue = (clone $baseQuery)->whereIn('status', ['paid', 'approved'])->sum('price');

        // 3. TABLE FILTERS (Search, Status, MOP)
        $tableQuery = clone $baseQuery;

        if ($request->filled('search_name')) {
            // Added trim() to prevent accidental spaces from breaking the search
            $tableQuery->where('player_name', 'like', '%' . trim($request->search_name) . '%');
        }

        if ($request->filled('filter_status')) {
            // Upgraded: whereRaw with LOWER() makes this 100% case-insensitive (Matches Paid, PAID, paid)
            $tableQuery->whereRaw('LOWER(status) = ?', [strtolower(trim($request->filter_status))]);
        }

        if ($request->filled('filter_mop')) {
            // Upgraded: Same exact-match case-insensitive logic for payment method
            $tableQuery->whereRaw('LOWER(payment_method) = ?', [strtolower(trim($request->filter_mop))]);
        }

        // 4. FINAL DATA & PAGINATION
        $reservations = $tableQuery->orderBy('reservation_date', 'desc')
                                   ->orderBy('start_time', 'desc')
                                   ->paginate(10)
                                   ->withQueryString(); // CRITICAL: Keeps filters active when clicking Page 2

        // 5. DYNAMIC CHART DATA (Last 7 Days)
        $days = [];
        $counts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->format('D'); // Generates 'Mon', 'Tue', etc. dynamically
            $counts[] = Reservation::whereDate('reservation_date', $date)->count(); // Counts real DB records
        }

        return view('admin.dashboard', compact(
            'reservations', 'total', 'paid', 'pending', 'revenue', 
            'days', 'counts', 'promo_mode', 'global_discount', 'active_tab'
        ));
    }

    public function approve($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'approved']);
        return back()->with('success', 'Booking approved!');
    }

    public function updatePromo(Request $request)
    {
        $request->validate(['global_discount' => 'required|numeric|min:0|max:100']);
        Cache::forever('global_discount', $request->global_discount);
        return back()->with('success', 'Discount updated successfully!');
    }

    public function walkin()
    {
        // Adjust this to your actual walk-in view name
        return view('admin.walkin'); 
    }
}