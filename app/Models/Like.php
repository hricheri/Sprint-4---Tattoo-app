<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    protected $fillable = [
        'liker_artist_id',
        'liked_artist_id',
    ];

    public function liker(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'liker_artist_id');
    }

    public function liked(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'liked_artist_id');
    }
}