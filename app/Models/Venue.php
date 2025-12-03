<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $primaryKey = 'venue_id';

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'capacity',
        'price_per_hour',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'price_per_hour' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Define the relationship with the User model (manager).
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'user_id');
    }

    /**
     * Define the relationship with the VenueImage model.
     */
    public function images()
    {
        return $this->hasMany(VenueImage::class, 'venue_id', 'venue_id');
    }
}