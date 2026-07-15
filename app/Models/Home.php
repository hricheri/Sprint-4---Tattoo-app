<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Home extends Model
{
    protected $fillable = [
        'artist_id',
        'roommates_count',
        'distance_to_studio_minutes',
        'transport_type',
        'transport_cost',
        'access_instructions',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}