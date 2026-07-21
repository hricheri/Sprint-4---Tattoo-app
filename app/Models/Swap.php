<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Swap extends Model
{
    protected $fillable = [
        'artist_a_id',
        'artist_b_id',
        'start_date',
        'end_date',
        'status',
        'includes_money_exchange',
        'promo_image',
        'promo_caption',
    ];

    public function artistA(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_a_id');
    }

    public function artistB(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_b_id');
    }
}