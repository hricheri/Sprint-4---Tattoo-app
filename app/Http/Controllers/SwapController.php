<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Like;
use App\Models\Swap;
use Illuminate\Http\Request;
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

        $pendingReceived = $swaps->where('status', 'pendiente')->where('artist_b_id', $myArtist->id);
        $pendingSent = $swaps->where('status', 'pendiente')->where('artist_a_id', $myArtist->id);
        $confirmed = $swaps->where('status', 'aceptado');

        $matchedIds = Like::where('liker_artist_id', $myArtist->id)
            ->whereIn('liked_artist_id', Like::where('liked_artist_id', $myArtist->id)->pluck('liker_artist_id'))
            ->pluck('liked_artist_id');

        $swapArtistIds = $swaps->flatMap(fn ($s) => [$s->artist_a_id, $s->artist_b_id])->unique();
        $newMatches = Artist::whereIn('id', $matchedIds)->whereNotIn('id', $swapArtistIds)->with('user')->get();

        return view('swaps.index', compact('pendingReceived', 'pendingSent', 'confirmed', 'newMatches', 'myArtist'));
    }

    public function create(Artist $artist)
    {
        $myArtist = Auth::user()->artist;

        return view('swaps.create', compact('artist', 'myArtist'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'artist_b_id' => 'required|exists:artists,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'includes_money_exchange' => 'nullable|boolean',
        ]);

        $myArtist = Auth::user()->artist;

        Swap::create([
            'artist_a_id' => $myArtist->id,
            'artist_b_id' => $validated['artist_b_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'pendiente',
            'includes_money_exchange' => $request->boolean('includes_money_exchange'),
        ]);

        return redirect()->route('swaps.index')->with('status', 'Swap proposal sent!');
    }

    public function accept(Swap $swap)
    {
        $this->authorizeParticipant($swap);

        $swap->update(['status' => 'aceptado']);

        return redirect()->route('swaps.index')->with('status', 'Swap confirmed! Check your travel calendar below.');
    }

    public function reject(Swap $swap)
    {
        $this->authorizeParticipant($swap);

        $swap->update(['status' => 'rechazado']);

        return redirect()->route('swaps.index')->with('status', 'Swap declined.');
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