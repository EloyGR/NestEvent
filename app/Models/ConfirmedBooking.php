<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmedBooking extends Model
{
    use HasFactory;

    protected $table = 'confirmed_bookings';
    protected $primaryKey = 'confirmation_id';

    protected $fillable = [
        'booking_id',
        'venue_id',
        'start_datetime',
        'end_datetime',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relacion con la reserva original.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Relacion con el local de la reserva confirmada.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
