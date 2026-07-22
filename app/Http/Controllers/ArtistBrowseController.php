<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtistBrowseController extends Controller
{
    public function explore()
    {
        $myArtist = Auth::user()->artist;

        if (!$myArtist) {
            return redirect()->route('artist-profile.create');
        }

        $likedIds = Like::where('liker_artist_id', $myArtist->id)->pluck('liked_artist_id')->toArray();
        $dismissedIds = session('dismissed_artist_ids', []);
        $excludedIds = array_merge($likedIds, $dismissedIds, [$myArtist->id]);

        $artist = Artist::with(['user', 'studio.features', 'home'])
            ->whereNotIn('id', $excludedIds)
            ->whereHas('studio')
            ->whereHas('home')
            ->first();

        $missingFeatures = collect();
        $additionalFeatures = collect();

        if ($artist) {
            $theirFeatureIds = $artist->studio->features->pluck('id')->toArray();
            $myPreferenceIds = $myArtist->featurePreferences->pluck('id')->toArray();

            $missingFeatures = $myArtist->featurePreferences->whereNotIn('id', $theirFeatureIds);
            $additionalFeatures = $artist->studio->features->whereNotIn('id', $myPreferenceIds);
        }

        return view('artist-browse.explore', compact('artist', 'missingFeatures', 'additionalFeatures'));
    }

    public function dismiss(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
        ]);

        $dismissed = session('dismissed_artist_ids', []);
        $dismissed[] = (int) $validated['artist_id'];
        session(['dismissed_artist_ids' => $dismissed]);

        return redirect()->route('explore');
    }

    public function like(Request $request)
    {
        $validated = $request->validate([
            'liked_artist_id' => 'required|exists:artists,id',
        ]);

        $myArtist = Auth::user()->artist;

        Like::firstOrCreate([
            'liker_artist_id' => $myArtist->id,
            'liked_artist_id' => $validated['liked_artist_id'],
        ]);

        return redirect()->route('explore')->with('status', 'Added to your favorites!');
    }

    public function favorites()
    {
        $myArtist = Auth::user()->artist;

        if (!$myArtist) {
            return redirect()->route('artist-profile.create');
        }

        $likes = Like::where('liker_artist_id', $myArtist->id)
            ->with('liked.user', 'liked.studio.features', 'liked.home')
            ->get();

        $matchedArtistIds = Like::where('liker_artist_id', $myArtist->id)
            ->whereIn('liked_artist_id', Like::where('liked_artist_id', $myArtist->id)->pluck('liker_artist_id'))
            ->pluck('liked_artist_id');

        $myPreferenceIds = $myArtist->featurePreferences->pluck('id')->toArray();

        return view('artist-browse.favorites', compact('likes', 'matchedArtistIds', 'myPreferenceIds'));
    }
}