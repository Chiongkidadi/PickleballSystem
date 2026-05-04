<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation; // Make sure this matches your model name!

class BookingController extends Controller
{
    public function index() { 
        return view('reserve'); 
    }

    public function store(Request $request) { 
        // Logic for saving 
    }

    public function adminDashboard(Request $request) {
        return view('admin.dashboard'); 
    }

    public function updateStatus(Reservation $reservation, $status) {
        $reservation->update(['status' => $status]);
        return back();
    }

    public function destroy(Reservation $reservation) {
        $reservation->delete();
        return back();
    }
}