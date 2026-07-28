<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Availability;
use App\Models\Like;
use App\Models\Swap;
use Illuminate\Support\Facades\Auth;

class SwapController extends Controller
{
    public function index()
    {
        $myArtist = Auth::user()->artist;

        if (!$myArtist) {
            return redirect()->route('artist-profile.create');
        }

        $swaps = Swap::where('artist_a_id', $myArtist->id)
            ->orWhere('artist_b_id', $myArtist->id)
            ->with(['artistA.user', 'artistA.studio', 'artistA.home', 'artistB.user', 'artistB.studio', 'artistB.home'])
            ->orderByDesc('created_at')
            ->get();

        $active = $swaps->where('status', 'pendiente');

        $awaitingAvailability = $active->filter(fn (Swap $s) => $s->isAwaitingAvailability());
        $awaitingConfirmation = $active->filter(fn (Swap $s) => $s->isAwaitingConfirmation());
        $confirmed = $swaps->where('status', 'aceptado');

        $newMatchIds = Like::pendingMatchArtistIdsFor($myArtist->id);
        $newMatches = Artist::whereIn('id', $newMatchIds)->with('user')->get();

        $hasSetAvailability = Availability::where('artist_id', $myArtist->id)->exists();

        return view('swaps.index', compact(
            'awaitingAvailability',
            'awaitingConfirmation',
            'confirmed',
            'newMatches',
            'myArtist',
            'hasSetAvailability'
        ));
    }

    public function start(Artist $artist)
    {
        $myArtist = Auth::user()->artist;

        $swap = Swap::startBetween($myArtist, $artist);

        return redirect()->route('availability', ['swap_id' => $swap->id]);
    }

    public function confirmDates(Swap $swap)
    {
        $this->authorizeParticipant($swap);

        $myArtist = Auth::user()->artist;
        $swap->confirmFor($myArtist->id);

        $message = $swap->status === 'aceptado'
            ? 'Swap confirmed! Check your travel calendar below.'
            : 'Dates confirmed on your side — waiting for the other artist to confirm.';

        return redirect()->route('swaps.index')->with('status', $message);
    }

    public function reject(Swap $swap)
    {
        $this->authorizeParticipant($swap);

        $swap->update(['status' => 'rechazado']);

        return redirect()->route('swaps.index')->with('status', 'Swap declined.');
    }

    public function markPromoSent(Swap $swap)
    {
        $this->authorizeParticipant($swap);

        $myArtist = Auth::user()->artist;
        $swap->markPromoSentFor($myArtist->id);

        return redirect()->route('swaps.index')->with('status', 'Nice — your guest announcement is marked as sent!');
    }

    private function authorizeParticipant(Swap $swap): void
    {
        $myArtist = Auth::user()->artist;

        abort_unless(
            $swap->artist_a_id === $myArtist->id || $swap->artist_b_id === $myArtist->id,
            403
        );
    }
}