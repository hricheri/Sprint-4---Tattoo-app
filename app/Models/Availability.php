<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = ['artist_id', 'date'];

    protected $casts = ['date' => 'date'];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}