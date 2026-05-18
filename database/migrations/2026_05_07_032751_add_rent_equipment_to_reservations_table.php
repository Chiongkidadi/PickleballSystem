<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Adds the rent_equipment column as a boolean (0 for false, 1 for true)
            // 'after' places it neatly after the status column in your database
            $table->boolean('rent_equipment')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Removes the column if you ever reverse the migration
            $table->dropColumn('rent_equipment');
        });
    }
};