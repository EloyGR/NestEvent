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
        // Tabla de horario semanal por local.
        Schema::create('venue_availability', function (Blueprint $table) {
            $table->id('availability_id');
            $table->foreignId('venue_id')->constrained('venues', 'venue_id');
            $table->tinyInteger('day_of_week')->comment('0=Domingo, 1=Lunes, ..., 6=Sabado');
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_available')->default(true);

            // Evita duplicar definiciones de horario para el mismo dia y local.
            $table->unique(['venue_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina la tabla de disponibilidad semanal.
        Schema::dropIfExists('venue_availability');
    }
};
