<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'name',
        'description',
        'start_datetime',
        'end_datetime',
        'organizer_id',
        'event_type',
        'expected_attendance',
        'is_public',
        'status',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_public' => 'boolean',
    ];

    /**
     * Relacion con el usuario organizador del evento.
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id', 'user_id');
    }
}