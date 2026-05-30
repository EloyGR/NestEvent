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
        // Tabla pivote para relacion muchos a muchos entre extras y locales.
        Schema::create('extra_venue', function (Blueprint $table) {
            $table->foreignId('extra_id')->constrained('extras', 'extra_id')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->timestamps();

            // Clave primaria compuesta para evitar pares duplicados.
            $table->primary(['extra_id', 'venue_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina la tabla pivote de extras y locales.
        Schema::dropIfExists('extra_venue');
    }
};
