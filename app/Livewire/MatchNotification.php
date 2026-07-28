<?php

namespace App\Livewire;

use App\Models\Like;
use Livewire\Component;

class MatchNotification extends Component
{
    public array $newMatches = [];

    public function mount(): void
    {
        $this->loadNewMatches();
    }

    public function loadNewMatches(): void
    {
        $artist = auth()->user()?->artist;

        if (! $artist) {
            $this->newMatches = [];
            return;
        }

        $this->newMatches = Like::unseenMatchesFor($artist->id)
            ->with('liked.user', 'liked.studio')
            ->get()
            ->map(fn (Like $like) => [
                'like_id' => $like->id,
                'artist_id' => $like->liked->id,
                'name' => $like->liked->user->name,
                'city' => $like->liked->studio->city ?? null,
                'photo' => $like->liked->profile_photo ? photo_url($like->liked->profile_photo) : null,
            ])
            ->toArray();
    }

    public function viewProfile(int $likeId, int $artistId)
    {
        Like::whereKey($likeId)->update(['match_seen_at' => now()]);

        return redirect()->route('artists.show', $artistId);
    }

    public function proposeSwap(int $likeId, int $artistId)
    {
        Like::whereKey($likeId)->update(['match_seen_at' => now()]);

        return redirect()->route('swaps.create', $artistId);
    }

    public function dismiss(int $likeId): void
    {
        Like::whereKey($likeId)->update(['match_seen_at' => now()]);

        $this->newMatches = collect($this->newMatches)
            ->reject(fn ($match) => $match['like_id'] === $likeId)
            ->values()
            ->toArray();
    }

    public function dismissAll(): void
    {
        collect($this->newMatches)->pluck('like_id')->each(
            fn ($id) => Like::whereKey($id)->update(['match_seen_at' => now()])
        );

        $this->newMatches = [];
    }

    public function render()
    {
        return view('livewire.match-notification');
    }
}