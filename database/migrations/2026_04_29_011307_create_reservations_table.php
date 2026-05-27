<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('player_name');
            $table->string('email')->nullable(); // Missing: Added for notifications
            $table->date('reservation_date'); 
            $table->string('start_time');
            $table->string('end_time'); // Missing: Controller calculates this
            $table->integer('duration');
            $table->integer('price')->default(0); 
            $table->string('payment_method')->nullable(); 
            $table->string('reference_number')->nullable(); // Missing: Added for GCash/Bank
            $table->string('proof_of_payment')->nullable(); // Missing: Added for receipt path
            $table->string('paddle_rental')->nullable(); // Missing: Added for the text description
            $table->integer('rent_equipment')->default(0);
            $table->string('status')->default('pending'); 
            $table->boolean('is_confirmed')->default(false); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};