<?php

namespace App\Models;

use App\Models\Artist;
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
        'confirmed_by_a',
        'confirmed_by_b',
        'confirmed_seen_by_a',
        'confirmed_seen_by_b',
        'promo_sent_by_a',
        'promo_sent_by_b',
        'includes_money_exchange',
        'cancellation_message',
        'cancelled_by_artist_id',
        'cancellation_resolved',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'includes_money_exchange' => 'boolean',
        'confirmed_by_a' => 'boolean',
        'confirmed_by_b' => 'boolean',
        'confirmed_seen_by_a' => 'boolean',
        'confirmed_seen_by_b' => 'boolean',
        'promo_sent_by_a' => 'boolean',
        'promo_sent_by_b' => 'boolean',
        'cancellation_resolved' => 'boolean',
    ];

    public function artistA(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_a_id');
    }

    public function artistB(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_b_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'cancelled_by_artist_id');
    }

    public static function isConfirmedBetween(int $artistIdA, int $artistIdB): bool
    {
        return static::confirmedSwapBetween($artistIdA, $artistIdB) !== null;
    }

    public static function confirmedSwapBetween(int $artistIdA, int $artistIdB): ?self
    {
        return static::where('status', 'aceptado')
            ->where(function ($q) use ($artistIdA, $artistIdB) {
                $q->where(function ($q2) use ($artistIdA, $artistIdB) {
                    $q2->where('artist_a_id', $artistIdA)->where('artist_b_id', $artistIdB);
                })->orWhere(function ($q2) use ($artistIdA, $artistIdB) {
                    $q2->where('artist_a_id', $artistIdB)->where('artist_b_id', $artistIdA);
                });
            })
            ->first();
    }

    public function isAwaitingAvailability(): bool
    {
        return $this->status === 'pendiente' && ! $this->start_date;
    }

    public function isAwaitingConfirmation(): bool
    {
        return $this->status === 'pendiente' && $this->start_date && ! ($this->confirmed_by_a && $this->confirmed_by_b);
    }

    public function myConfirmed(int $artistId): bool
    {
        if ($this->artist_a_id === $artistId) return (bool) $this->confirmed_by_a;
        if ($this->artist_b_id === $artistId) return (bool) $this->confirmed_by_b;
        return false;
    }

    public function otherArtistId(int $myArtistId): int
    {
        return $this->artist_a_id === $myArtistId ? $this->artist_b_id : $this->artist_a_id;
    }

    public function needsConfirmedPopupFor(int $artistId): bool
    {
        if ($this->status !== 'aceptado') {
            return false;
        }

        if ($this->artist_a_id === $artistId) return ! $this->confirmed_seen_by_a;
        if ($this->artist_b_id === $artistId) return ! $this->confirmed_seen_by_b;

        return false;
    }

    public function promoSentFor(int $artistId): bool
    {
        if ($this->artist_a_id === $artistId) return (bool) $this->promo_sent_by_a;
        if ($this->artist_b_id === $artistId) return (bool) $this->promo_sent_by_b;
        return false;
    }

    public function bothPromosSent(): bool
    {
        return (bool) $this->promo_sent_by_a && (bool) $this->promo_sent_by_b;
    }

    public function isPromoReminderUrgent(): bool
    {
        return $this->start_date && now()->diffInDays($this->start_date, false) <= 7 && now()->lte($this->start_date);
    }

    public function isActiveFor(int $artistId): bool
    {
        return in_array($this->status, ['pendiente', 'aceptado'])
            && ($this->artist_a_id === $artistId || $this->artist_b_id === $artistId);
    }

    public function wasCancelledByOther(int $myArtistId): bool
    {
        return $this->status === 'cancelado'
            && $this->cancelled_by_artist_id !== null
            && $this->cancelled_by_artist_id !== $myArtistId;
    }
}