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

    public static function mutualMatchArtistIdsFor(int $artistId): Collection
    {
        return static::where('liker_artist_id', $artistId)
            ->whereIn('liked_artist_id', static::where('liked_artist_id', $artistId)->pluck('liker_artist_id'))
            ->pluck('liked_artist_id');
    }

    /**
     * Matches mutuos con $artistId que todavía no tienen NINGÚN swap
     * pendiente, aceptado, o cancelado con esa persona. Un swap "rechazado"
     * (fechas propuestas que no llegaron a confirmarse) SÍ permite reintentar
     * — pero un swap cancelado (después de haber estado confirmado) es un
     * compromiso real que se rompió, y bloquea el match para siempre, para
     * no volver a sorprender con el pop-up a alguien que ya decidió seguir
     * su búsqueda por otro lado.
     */
    public static function pendingMatchArtistIdsFor(int $artistId): Collection
    {
        $matchedIds = static::mutualMatchArtistIdsFor($artistId);

        $blockedArtistIds = Swap::where(function ($q) use ($artistId) {
                $q->where('artist_a_id', $artistId)->orWhere('artist_b_id', $artistId);
            })
            ->whereIn('status', ['pendiente', 'aceptado', 'cancelado'])
            ->get()
            ->flatMap(fn (Swap $s) => [$s->artist_a_id, $s->artist_b_id])
            ->unique();

        return $matchedIds->diff($blockedArtistIds)->values();
    }

    public static function unseenMatchesFor(int $artistId)
    {
        return static::where('liker_artist_id', $artistId)
            ->whereNull('match_seen_at')
            ->whereIn('liked_artist_id', static::pendingMatchArtistIdsFor($artistId));
    }
}