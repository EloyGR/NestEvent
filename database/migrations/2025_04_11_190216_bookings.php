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
        // Tabla de reservas entre eventos y locales.
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('event_id')->constrained('events', 'event_id');
            $table->foreignId('venue_id')->constrained('venues', 'venue_id');
            $table->enum('booking_status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->foreignId('approved_by')->nullable()->constrained('users', 'user_id');
            $table->dateTime('approval_date')->nullable();
            $table->text('notes')->nullable();

            // Evita reservas duplicadas para la misma combinacion evento-local.
            $table->unique(['event_id', 'venue_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina la tabla de reservas.
        Schema::dropIfExists('bookings');
    }
};
