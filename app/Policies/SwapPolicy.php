<?php

namespace App\Policies;

use App\Models\Swap;
use App\Models\User;

class SwapPolicy
{
    /**
     * Determina si el usuario participa de este swap (como artist_a o artist_b).
     */
    public function view(User $user, Swap $swap): bool
    {
        return $this->isParticipant($user, $swap);
    }

    public function confirmDates(User $user, Swap $swap): bool
    {
        return $this->isParticipant($user, $swap) && $swap->status === 'pendiente';
    }

    public function reject(User $user, Swap $swap): bool
    {
        return $this->isParticipant($user, $swap) && $swap->status === 'pendiente';
    }

    public function cancel(User $user, Swap $swap): bool
    {
        return $this->isParticipant($user, $swap) && $swap->status === 'aceptado';
    }

    public function markPromoSent(User $user, Swap $swap): bool
    {
        return $this->isParticipant($user, $swap) && $swap->status === 'aceptado';
    }

    private function isParticipant(User $user, Swap $swap): bool
    {
        $artist = $user->artist;

        if (! $artist) {
            return false;
        }

        return $swap->artist_a_id === $artist->id || $swap->artist_b_id === $artist->id;
    }
}