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
        // Tabla de locales gestionados por usuarios con rol manager.
        Schema::create('venues', function (Blueprint $table) {
            $table->id('venue_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('address', 100);
            $table->string('city', 50);
            $table->string('state', 50)->nullable();
            $table->string('zip_code', 20);
            $table->string('country', 50);
            $table->integer('capacity');
            $table->decimal('price_per_hour', 10, 2)->nullable();
            $table->foreignId('manager_id')->constrained('users', 'user_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Desactiva FK para permitir eliminar la tabla sin conflictos de referencia.
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('venues');
        Schema::enableForeignKeyConstraints();
    }
};
