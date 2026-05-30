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
        // Tabla de excepciones sobre la disponibilidad regular de cada local.
        Schema::create('availability_exceptions', function (Blueprint $table) {
            $table->id('exception_id');
            $table->foreignId('venue_id')->constrained('venues', 'venue_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_available')->default(false);
            $table->string('reason', 255)->nullable();

            // Indice para consultas por local y rango de fechas.
            $table->index(['venue_id', 'start_date', 'end_date'], 'idx_availability_exceptions_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina la tabla de excepciones de disponibilidad.
        Schema::dropIfExists('availability_exceptions');
    }
};
