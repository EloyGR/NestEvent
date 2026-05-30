<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    use HasFactory;

    protected $primaryKey = 'extra_id';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
    ];

    /**
     * Relacion muchos a muchos con locales.
     */
    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'extra_venue', 'extra_id', 'venue_id', 'extra_id', 'venue_id')
            ->withTimestamps();
    }
}
