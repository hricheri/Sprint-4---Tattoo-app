<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Home extends Model
{
    protected $fillable = [
        'artist_id',
        'roommates_count',
        'description',
        'distance_to_studio_minutes',
        'access_instructions',
        'photo',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}