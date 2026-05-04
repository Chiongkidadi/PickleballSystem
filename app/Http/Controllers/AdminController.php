<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Reservation::query();
        $reportType = $request->get('report', 'all');

        // Filter Logic
        if ($reportType == 'day') {
            $query->whereDate('reservation_date', Carbon::today());
            $reportTitle = "Daily Report: " . Carbon::today()->format('M d, Y');
        } elseif ($reportType == 'month') {
            $query->whereMonth('reservation_date', Carbon::now()->month)
                  ->whereYear('reservation_date', Carbon::now()->year);
            $reportTitle = "Monthly Report: " . Carbon::now()->format('F Y');
        } elseif ($reportType == 'year') {
            $query->whereYear('reservation_date', Carbon::now()->year);
            $reportTitle = "Annual Report: " . Carbon::now()->year;
        } else {
            $reportTitle = "Overall Summary Report";
        }

        $reservations = $query->orderBy('reservation_date', 'desc')->get();

        // Calculations
        $total = $reservations->count();
        $paid = $reservations->where('status', 'paid')->count();
        $pending = $reservations->where('status', 'pending')->count();
        $revenue = $reservations->where('status', 'paid')->sum('price');

        // Chart Data (Last 7 Days)
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $counts = [5, 10, 15, 7, 20, 25, 30]; 

        return view('admin.dashboard', compact(
            'reservations', 'total', 'paid', 'pending', 'revenue', 'days', 'counts', 'reportTitle'
        ));
    }
}