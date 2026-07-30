<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\Availability;
use App\Models\Swap;

class SwapService
{
    public function start(Artist $initiator, Artist $other): Swap
    {
        $existing = Swap::where('status', 'pendiente')
            ->where(function ($q) use ($initiator, $other) {
                $q->where(function ($q2) use ($initiator, $other) {
                    $q2->where('artist_a_id', $initiator->id)->where('artist_b_id', $other->id);
                })->orWhere(function ($q2) use ($initiator, $other) {
                    $q2->where('artist_a_id', $other->id)->where('artist_b_id', $initiator->id);
                });
            })
            ->first();

        $swap = $existing ?? Swap::create([
            'artist_a_id' => $initiator->id,
            'artist_b_id' => $other->id,
            'status' => 'pendiente',
            'includes_money_exchange' => false,
        ]);

        $this->recalculateDates($swap);

        return $swap;
    }

    public function recalculateDates(Swap $swap): void
    {
        $datesA = Availability::where('artist_id', $swap->artist_a_id)
            ->pluck('date')->map(fn ($d) => $d->format('Y-m-d'))->toArray();

        $datesB = Availability::where('artist_id', $swap->artist_b_id)
            ->pluck('date')->map(fn ($d) => $d->format('Y-m-d'))->toArray();

        $overlap = array_values(array_intersect($datesA, $datesB));
        sort($overlap);

        $newStart = $overlap[0] ?? null;
        $newEnd = $overlap[count($overlap) - 1] ?? null;

        $currentStart = $swap->start_date?->format('Y-m-d');
        $currentEnd = $swap->end_date?->format('Y-m-d');

        if ($currentStart !== $newStart || $currentEnd !== $newEnd) {
            $swap->update([
                'start_date' => $newStart,
                'end_date' => $newEnd,
                'confirmed_by_a' => false,
                'confirmed_by_b' => false,
            ]);
        }
    }

    public function recalculateAllFor(int $artistId): void
    {
        Swap::where('status', 'pendiente')
            ->where(function ($q) use ($artistId) {
                $q->where('artist_a_id', $artistId)->orWhere('artist_b_id', $artistId);
            })
            ->get()
            ->each(fn (Swap $swap) => $this->recalculateDates($swap));
    }

    public function confirm(Swap $swap, int $artistId): void
    {
        if ($swap->artist_a_id === $artistId) {
            $swap->confirmed_by_a = true;
        } elseif ($swap->artist_b_id === $artistId) {
            $swap->confirmed_by_b = true;
        }

        if ($swap->confirmed_by_a && $swap->confirmed_by_b) {
            $swap->status = 'aceptado';
        }

        $swap->save();
    }

    public function reject(Swap $swap): void
    {
        $swap->update(['status' => 'rechazado']);
    }

    public function cancel(Swap $swap, int $cancelledByArtistId, ?string $message = null): void
    {
        $swap->update([
            'status' => 'cancelado',
            'cancelled_by_artist_id' => $cancelledByArtistId,
            'cancellation_message' => $message,
            'cancellation_resolved' => false,
        ]);
    }

    /**
     * Marca la cancelación como "resuelta" — recién a partir de esto la
     * pareja de artistas vuelve a poder aparecer como match nuevo.
     */
    public function resolveCancellation(Swap $swap): void
    {
        $swap->update(['cancellation_resolved' => true]);
    }

    public function markConfirmedSeen(Swap $swap, int $artistId): void
    {
        if ($swap->artist_a_id === $artistId) {
            $swap->confirmed_seen_by_a = true;
        } elseif ($swap->artist_b_id === $artistId) {
            $swap->confirmed_seen_by_b = true;
        }

        $swap->save();
    }

    public function markPromoSent(Swap $swap, int $artistId): void
    {
        if ($swap->artist_a_id === $artistId) {
            $swap->promo_sent_by_a = true;
        } elseif ($swap->artist_b_id === $artistId) {
            $swap->promo_sent_by_b = true;
        }

        $swap->save();
    }
}