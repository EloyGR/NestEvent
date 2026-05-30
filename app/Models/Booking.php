<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'event_id',
        'venue_id',
        'booking_status',
        'start_datetime',
        'end_datetime',
        'approved_by',
        'approval_date',
        'notes',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'approval_date' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relacion con el evento asociado a la reserva.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Relacion con el local reservado.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    /**
     * Relacion con el usuario que aprueba la reserva.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relacion con el registro de confirmacion de la reserva.
     */
    public function confirmedBooking()
    {
        return $this->hasOne(ConfirmedBooking::class, 'booking_id', 'booking_id');
    }
}
