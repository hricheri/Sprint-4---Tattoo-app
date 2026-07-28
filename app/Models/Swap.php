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
        'promo_image',
        'promo_caption',
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

    /**
     * Crea (o reusa, si ya existe uno activo) un swap "pendiente" entre dos
     * artistas, sin fechas todavía, y calcula la intersección inicial.
     */
    public static function startBetween(Artist $initiator, Artist $other): self
    {
        $existing = static::where('status', 'pendiente')
            ->where(function ($q) use ($initiator, $other) {
                $q->where(function ($q2) use ($initiator, $other) {
                    $q2->where('artist_a_id', $initiator->id)->where('artist_b_id', $other->id);
                })->orWhere(function ($q2) use ($initiator, $other) {
                    $q2->where('artist_a_id', $other->id)->where('artist_b_id', $initiator->id);
                });
            })
            ->first();

        $swap = $existing ?? static::create([
            'artist_a_id' => $initiator->id,
            'artist_b_id' => $other->id,
            'status' => 'pendiente',
            'includes_money_exchange' => false,
        ]);

        $swap->recalculateDates();

        return $swap;
    }

    public function recalculateDates(): void
    {
        $datesA = Availability::where('artist_id', $this->artist_a_id)
            ->pluck('date')->map(fn ($d) => $d->format('Y-m-d'))->toArray();

        $datesB = Availability::where('artist_id', $this->artist_b_id)
            ->pluck('date')->map(fn ($d) => $d->format('Y-m-d'))->toArray();

        $overlap = array_values(array_intersect($datesA, $datesB));
        sort($overlap);

        $newStart = $overlap[0] ?? null;
        $newEnd = $overlap[count($overlap) - 1] ?? null;

        $currentStart = $this->start_date?->format('Y-m-d');
        $currentEnd = $this->end_date?->format('Y-m-d');

        if ($currentStart !== $newStart || $currentEnd !== $newEnd) {
            $this->update([
                'start_date' => $newStart,
                'end_date' => $newEnd,
                'confirmed_by_a' => false,
                'confirmed_by_b' => false,
            ]);
        }
    }

    public static function recalculateAllFor(int $artistId): void
    {
        static::where('status', 'pendiente')
            ->where(function ($q) use ($artistId) {
                $q->where('artist_a_id', $artistId)->orWhere('artist_b_id', $artistId);
            })
            ->get()
            ->each(fn (Swap $swap) => $swap->recalculateDates());
    }

    public function confirmFor(int $artistId): void
    {
        if ($this->artist_a_id === $artistId) {
            $this->confirmed_by_a = true;
        } elseif ($this->artist_b_id === $artistId) {
            $this->confirmed_by_b = true;
        }

        if ($this->confirmed_by_a && $this->confirmed_by_b) {
            $this->status = 'aceptado';
        }

        $this->save();
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

    public function markConfirmedSeenFor(int $artistId): void
    {
        if ($this->artist_a_id === $artistId) {
            $this->confirmed_seen_by_a = true;
        } elseif ($this->artist_b_id === $artistId) {
            $this->confirmed_seen_by_b = true;
        }

        $this->save();
    }

    public function promoSentFor(int $artistId): bool
    {
        if ($this->artist_a_id === $artistId) return (bool) $this->promo_sent_by_a;
        if ($this->artist_b_id === $artistId) return (bool) $this->promo_sent_by_b;
        return false;
    }

    public function markPromoSentFor(int $artistId): void
    {
        if ($this->artist_a_id === $artistId) {
            $this->promo_sent_by_a = true;
        } elseif ($this->artist_b_id === $artistId) {
            $this->promo_sent_by_b = true;
        }

        $this->save();
    }

    public function bothPromosSent(): bool
    {
        return (bool) $this->promo_sent_by_a && (bool) $this->promo_sent_by_b;
    }

    public function isPromoReminderUrgent(): bool
    {
        return $this->start_date && now()->diffInDays($this->start_date, false) <= 7 && now()->lte($this->start_date);
    }
}