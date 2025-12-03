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
            
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
