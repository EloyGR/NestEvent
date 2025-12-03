<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use InvalidArgumentException;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'profile_picture',
        'user_type',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function setUserTypeAttribute($value)
    {
        $allowedTypes = ['admin', 'event_manager', 'local_manager', 'user'];

        if (!in_array($value, $allowedTypes)) {
            throw new InvalidArgumentException("Invalid user_type: $value");
        }

        $this->attributes['user_type'] = $value;
    }

    public function organizedEvents()
    {
        return $this->hasMany(Event::class, 'organizer_id', 'user_id');
    }

    public function managedVenues()
    {
        return $this->hasMany(Venue::class, 'manager_id', 'user_id');
    }
}