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
        // Tabla de notificaciones internas por usuario.
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->string('title', 100);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('notification_type', 50);
            $table->enum('related_entity_type', ['event', 'venue', 'booking', 'user'])->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            // Indice para lectura rapida de no leidas por usuario.
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina la tabla de notificaciones.
        Schema::dropIfExists('notifications');
    }
};
