<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Availability;
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

        $candidates = Artist::with(['user', 'studio.features', 'home'])
            ->whereNotIn('id', $excludedIds)
            ->whereHas('studio')
            ->whereHas('home')
            ->get();

        $artist = null;
        $matchedByCancelledDates = false;
        $suggestedOverlapRanges = [];

        $searchDates = session('availability_search_dates');

        if ($searchDates && $candidates->isNotEmpty()) {
            $wantedDates = $this->datesInRange($searchDates['start'], $searchDates['end']);

            $prioritized = $candidates->first(function (Artist $candidate) use ($wantedDates, &$suggestedOverlapRanges) {
                $theirDates = Availability::where('artist_id', $candidate->id)
                    ->pluck('date')
                    ->map(fn ($d) => $d->format('Y-m-d'))
                    ->toArray();

                $overlap = array_values(array_intersect($wantedDates, $theirDates));
                sort($overlap);

                if (count($overlap) > 0) {
                    $suggestedOverlapRanges = $this->groupConsecutiveDates($overlap);
                    return true;
                }

                return false;
            });

            if ($prioritized) {
                $artist = $prioritized;
                $matchedByCancelledDates = true;
            }
        }

        if (! $artist) {
            $artist = $candidates->first();
        }

        $missingFeatures = collect();
        $additionalFeatures = collect();

        if ($artist) {
            $theirFeatureIds = $artist->studio->features->pluck('id')->toArray();
            $myPreferenceIds = $myArtist->featurePreferences->pluck('id')->toArray();

            $missingFeatures = $myArtist->featurePreferences->whereNotIn('id', $theirFeatureIds);
            $additionalFeatures = $artist->studio->features->whereNotIn('id', $myPreferenceIds);
        }

        return view('artist-browse.explore', compact(
            'artist',
            'missingFeatures',
            'additionalFeatures',
            'matchedByCancelledDates',
            'suggestedOverlapRanges'
        ));
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

        session()->forget('availability_search_dates');

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

        return view('artist-browse.favorites', compact('likes', 'matchedArtistIds', 'myPreferenceIds', 'myArtist'));
    }

    private function datesInRange(string $start, string $end): array
    {
        $dates = [];
        $current = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);

        while ($current->lte($endDate)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return $dates;
    }

    private function groupConsecutiveDates(array $sortedDates): array
    {
        $ranges = [];
        $rangeStart = $sortedDates[0];
        $rangeEnd = $sortedDates[0];

        for ($i = 1; $i < count($sortedDates); $i++) {
            $prev = \Carbon\Carbon::parse($sortedDates[$i - 1]);
            $current = \Carbon\Carbon::parse($sortedDates[$i]);

            if ($prev->diffInDays($current) === 1) {
                $rangeEnd = $sortedDates[$i];
            } else {
                $ranges[] = ['start' => $rangeStart, 'end' => $rangeEnd];
                $rangeStart = $sortedDates[$i];
                $rangeEnd = $sortedDates[$i];
            }
        }

        $ranges[] = ['start' => $rangeStart, 'end' => $rangeEnd];

        return $ranges;
    }
}