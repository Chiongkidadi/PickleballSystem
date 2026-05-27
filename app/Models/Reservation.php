<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_name',
        'email',
        'reservation_date',
        'start_time',
        'end_time', 
        'duration',
        'price',
        'payment_method',
        'status',
        'rent_equipment',
        'reference_number',   
        'proof_of_payment', 
        'paddle_rental',  
        'rent_equipment'  
    ];
}