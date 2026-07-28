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

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'includes_money_exchange' => 'boolean',
    ];

    public function artistA(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_a_id');
    }

    public function artistB(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_b_id');
    }

    public static function isConfirmedBetween(int $artistIdA, int $artistIdB): bool
    {
        return static::where('status', 'aceptado')
            ->where(function ($q) use ($artistIdA, $artistIdB) {
                $q->where(function ($q2) use ($artistIdA, $artistIdB) {
                    $q2->where('artist_a_id', $artistIdA)->where('artist_b_id', $artistIdB);
                })->orWhere(function ($q2) use ($artistIdA, $artistIdB) {
                    $q2->where('artist_a_id', $artistIdB)->where('artist_b_id', $artistIdA);
                });
            })
            ->exists();
    }
}