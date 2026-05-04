<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon; 

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Generate Chart Data (Last 7 Days)
        $days = [];
        $counts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('D'); // Gets 'Mon', 'Tue', etc.
            
            // Count how many reservations happened on this specific day
            $counts[] = Reservation::whereDate('reservation_date', $date->format('Y-m-d'))->count();
        }

        // 2. Fetch Dashboard Metrics
        $data = [
            'total' => Reservation::count(),
            'paid' => Reservation::where('status', 'paid')->count(),
            'pending' => Reservation::where('status', 'pending')->count(),
            
            // FIXED: Sums up ALL reservations for projected revenue
            'revenue' => Reservation::sum('price'), 
            
            'reservations' => Reservation::orderBy('reservation_date', 'desc')->get(),
            
            // 3. Pass chart data to the dashboard.blade.php
            'days' => $days,
            'counts' => $counts,
        ];

        return view('admin.dashboard', $data);
    }

    /**
     * Handle the Approve Button on the Dashboard
     */
    public function approve($id)
    {
        $reservation = Reservation::findOrFail($id);
        
        // Changes the status from 'pending' to 'paid' when you click the green button
        $reservation->update(['status' => 'paid']);
        
        return back()->with('success', 'Payment confirmed.');
    }

    /**
     * Handle the Walk-in Booking Button
     */
    public function walkin()
    {
        // FIXED: Added 'admin.' prefix so it finds the correct route
        return redirect()->route('admin.reserve');
    }
}