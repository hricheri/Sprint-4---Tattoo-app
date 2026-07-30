<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Availability;
use App\Models\Like;
use App\Models\Swap;
use App\Services\SwapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwapController extends Controller
{
    public function __construct(private SwapService $swaps)
    {
    }

    public function index()
    {
        $myArtist = Auth::user()->artist;

        if (!$myArtist) {
            return redirect()->route('artist-profile.create');
        }

        $swaps = Swap::where('artist_a_id', $myArtist->id)
            ->orWhere('artist_b_id', $myArtist->id)
            ->with(['artistA.user', 'artistA.studio', 'artistA.home', 'artistB.user', 'artistB.studio', 'artistB.home', 'cancelledBy.user'])
            ->orderByDesc('created_at')
            ->get();

        $active = $swaps->where('status', 'pendiente');

        $awaitingAvailability = $active->filter(fn (Swap $s) => $s->isAwaitingAvailability());
        $awaitingConfirmation = $active->filter(fn (Swap $s) => $s->isAwaitingConfirmation());
        $confirmed = $swaps->where('status', 'aceptado');

        $recentlyCancelled = $swaps
            ->where('status', 'cancelado')
            ->filter(fn (Swap $s) => $s->wasCancelledByOther($myArtist->id) && !session()->has("cancelled_swap_dismissed_{$s->id}"));

        $newMatchIds = Like::pendingMatchArtistIdsFor($myArtist->id);
        $newMatches = Artist::whereIn('id', $newMatchIds)->with('user')->get();

        $hasSetAvailability = Availability::where('artist_id', $myArtist->id)->exists();

        return view('swaps.index', compact(
            'awaitingAvailability',
            'awaitingConfirmation',
            'confirmed',
            'recentlyCancelled',
            'newMatches',
            'myArtist',
            'hasSetAvailability'
        ));
    }

    public function start(Artist $artist)
    {
        $myArtist = Auth::user()->artist;

        $swap = $this->swaps->start($myArtist, $artist);

        Like::where('liker_artist_id', $myArtist->id)
            ->where('liked_artist_id', $artist->id)
            ->update(['match_seen_at' => now()]);

        return redirect()->route('availability', ['swap_id' => $swap->id]);
    }

    public function confirmDates(Swap $swap)
    {
        $this->authorize('confirmDates', $swap);

        $myArtist = Auth::user()->artist;
        $this->swaps->confirm($swap, $myArtist->id);

        $message = $swap->fresh()->status === 'aceptado'
            ? 'Swap confirmed! Check your travel calendar below.'
            : 'Dates confirmed on your side — waiting for the other artist to confirm.';

        return redirect()->route('swaps.index')->with('status', $message);
    }

    public function reject(Swap $swap)
    {
        $this->authorize('reject', $swap);

        $this->swaps->reject($swap);

        return redirect()->route('swaps.index')->with('status', 'Swap declined.');
    }

    public function cancel(Request $request, Swap $swap)
    {
        $this->authorize('cancel', $swap);

        $validated = $request->validate([
            'cancellation_message' => 'nullable|string|max:500',
        ]);

        $myArtist = Auth::user()->artist;
        $this->swaps->cancel($swap, $myArtist->id, $validated['cancellation_message'] ?? null);

        return redirect()->route('swaps.index')->with('status', 'Swap cancelled.');
    }

    public function dismissCancellation(Request $request, Swap $swap)
    {
        $this->swaps->resolveCancellation($swap);

        session(["cancelled_swap_dismissed_{$swap->id}" => true]);

        return redirect()->route('swaps.index');
    }

    public function searchAfterCancellation(Request $request, Swap $swap)
    {
        $myArtist = Auth::user()->artist;

        abort_unless($swap->artist_a_id === $myArtist->id || $swap->artist_b_id === $myArtist->id, 403);

        if ($swap->start_date && $swap->end_date) {
            session([
                'availability_search_dates' => [
                    'start' => $swap->start_date->format('Y-m-d'),
                    'end' => $swap->end_date->format('Y-m-d'),
                ],
            ]);
        }

        $this->swaps->resolveCancellation($swap);
        session(["cancelled_swap_dismissed_{$swap->id}" => true]);

        return redirect()->route('explore');
    }

    public function markPromoSent(Swap $swap)
    {
        $this->authorize('markPromoSent', $swap);

        $myArtist = Auth::user()->artist;
        $this->swaps->markPromoSent($swap, $myArtist->id);

        return redirect()->route('swaps.index')->with('status', "Announcement underway! Now let the clients roll in!");
    }
}