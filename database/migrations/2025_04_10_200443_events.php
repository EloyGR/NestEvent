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
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->foreignId('organizer_id')->constrained('users', 'user_id');
            $table->string('event_type', 50)->nullable();
            $table->integer('expected_attendance')->nullable();
            $table->boolean('is_public')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints(); // Deshabilita las restricciones de clave foránea
        Schema::dropIfExists('events');
        Schema::enableForeignKeyConstraints(); // Habilita las restricciones de clave foránea
    }
};
