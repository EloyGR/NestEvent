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
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id('attendee_id');
            $table->foreignId('event_id')->constrained('events', 'event_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->timestamp('registration_date')->useCurrent();
            $table->enum('attendance_status', ['registered', 'attended', 'cancelled'])->default('registered');
            
            $table->unique(['event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};
