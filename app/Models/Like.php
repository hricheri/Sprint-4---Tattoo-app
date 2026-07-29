<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Like extends Model
{
    protected $fillable = [
        'liker_artist_id',
        'liked_artist_id',
        'match_seen_at',
    ];

    protected $casts = [
        'match_seen_at' => 'datetime',
    ];

    public function liker(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'liker_artist_id');
    }

    public function liked(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'liked_artist_id');
    }

    /**
     * IDs de artistas que dieron like mutuo con $artistId (match confirmado),
     * sin importar si ya se vio el pop-up o si ya existe un swap.
     */
    public static function mutualMatchArtistIdsFor(int $artistId): Collection
    {
        return static::where('liker_artist_id', $artistId)
            ->whereIn('liked_artist_id', static::where('liked_artist_id', $artistId)->pluck('liker_artist_id'))
            ->pluck('liked_artist_id');
    }

    /**
     * De los matches mutuos de $artistId, los que todavía no generaron
     * ningún swap activo (pendiente o aceptado). Un swap rechazado o
     * cancelado NO bloquea que vuelva a aparecer como "new match".
     */
    public static function pendingMatchArtistIdsFor(int $artistId): Collection
    {
        $matchedIds = static::mutualMatchArtistIdsFor($artistId);

        $activeSwapArtistIds = Swap::where(function ($q) use ($artistId) {
                $q->where('artist_a_id', $artistId)->orWhere('artist_b_id', $artistId);
            })
            ->whereIn('status', ['pendiente', 'aceptado'])
            ->get()
            ->flatMap(fn (Swap $s) => [$s->artist_a_id, $s->artist_b_id])
            ->unique();

        return $matchedIds->diff($activeSwapArtistIds)->values();
    }

    /**
     * Matches mutuos de $artistId que esa persona (como liker) todavía
     * no descartó del pop-up de notificación, y que no tienen ya un
     * swap activo (pendiente o aceptado) en curso.
     */
    public static function unseenMatchesFor(int $artistId)
    {
        return static::where('liker_artist_id', $artistId)
            ->whereNull('match_seen_at')
            ->whereIn('liked_artist_id', static::pendingMatchArtistIdsFor($artistId));
    }
}