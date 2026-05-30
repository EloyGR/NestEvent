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
        // Tabla de reservas confirmadas para control de solapes y estado activo.
        Schema::create('confirmed_bookings', function (Blueprint $table) {
            $table->id('confirmation_id');
            $table->foreignId('booking_id')->unique()->constrained('bookings', 'booking_id')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->timestamp('confirmed_at')->useCurrent();
            $table->timestamp('cancelled_at')->nullable();

            // Indice para busqueda por rango horario dentro del local.
            $table->index(['venue_id', 'start_datetime', 'end_datetime'], 'idx_confirmed_bookings_time_range');
            // Indice para distinguir reservas activas frente a canceladas.
            $table->index(['venue_id', 'cancelled_at'], 'idx_confirmed_bookings_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina la tabla de reservas confirmadas.
        Schema::dropIfExists('confirmed_bookings');
    }
};
