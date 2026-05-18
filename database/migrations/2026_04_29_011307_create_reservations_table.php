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
            $table->date('reservation_date'); 
            $table->string('player_name');
            
            // --- ADDED THE EMAIL COLUMN HERE ---
            $table->string('email')->nullable();
            
            $table->string('start_time');
            
            // --- ADDED THESE NEW COLUMNS ---
            $table->string('end_time'); 
            $table->string('duration');
            $table->integer('price')->default(0); 
            $table->string('payment_method')->nullable(); 
            $table->string('status')->default('pending'); 
            
            // *** HERE IS THE MISSING COLUMN ***
            $table->boolean('rent_equipment')->default(0); 
            // -------------------------------
            
            $table->boolean('is_confirmed')->default(false); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};