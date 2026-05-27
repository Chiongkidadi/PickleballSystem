<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $rescheduleUrl;
    public $isReschedule;

    public function __construct($reservation, $rescheduleUrl)
    {
        $this->reservation = $reservation;
        $this->rescheduleUrl = $rescheduleUrl;
        
        // We detect if this is a reschedule by checking if the request had a booking_id
        $this->isReschedule = request()->filled('booking_id');
    }

    public function build()
    {
        // 1. Determine the subject based on the reschedule status
        $subjectText = $this->isReschedule 
            ? 'PICKLEBALL RESCHEDULE RECEIPT - Island Central Mactan' 
            : 'Pickleball Booking Receipt - Island Central Mactan';

        // 2. Return the view and the dynamic subject
        return $this->subject($subjectText)
                    ->view('emails.receipt');
    }
}