<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // The "approved list" for saving to the database
    protected $fillable = [
        'reservation_date',
        'player_name',
        'start_time',
        'end_time',        // Added so your controller can save the end time
        'duration',
        'is_confirmed',    // Kept your original field
        'status',          // Added so it can save 'pending' or 'paid'
        'price',           // Added so it can save the calculated fee
        'payment_method'   // Added so it can save 'Cash' or 'Online'
    ];
}