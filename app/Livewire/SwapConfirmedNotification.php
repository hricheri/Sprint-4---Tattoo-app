<?php

namespace App\Livewire;

use App\Models\Swap;
use App\Services\SwapService;
use Livewire\Component;

class SwapConfirmedNotification extends Component
{
    public array $confirmedSwaps = [];

    public function mount(): void
    {
        $this->loadConfirmedSwaps();
    }

    public function loadConfirmedSwaps(): void
    {
        $artist = auth()->user()?->artist;

        if (! $artist) {
            $this->confirmedSwaps = [];
            return;
        }

        $swaps = Swap::where('status', 'aceptado')
            ->where(function ($q) use ($artist) {
                $q->where('artist_a_id', $artist->id)->orWhere('artist_b_id', $artist->id);
            })
            ->with(['artistA.user', 'artistA.studio', 'artistB.user', 'artistB.studio'])
            ->get()
            ->filter(fn (Swap $s) => $s->needsConfirmedPopupFor($artist->id));

        $this->confirmedSwaps = $swaps->map(function (Swap $swap) use ($artist) {
            $other = $swap->artist_a_id === $artist->id ? $swap->artistB : $swap->artistA;

            return [
                'swap_id' => $swap->id,
                'name' => $other->user->name,
                'city' => $other->studio->city ?? '',
                'start' => $swap->start_date->format('M j'),
                'end' => $swap->end_date->format('M j, Y'),
                'photo' => $other->profile_photo ? photo_url($other->profile_photo) : null,
            ];
        })->values()->toArray();
    }

    public function dismiss(int $swapId, SwapService $swaps): void
    {
        $artist = auth()->user()->artist;
        $swap = Swap::find($swapId);

        if ($swap) {
            $swaps->markConfirmedSeen($swap, $artist->id);
        }

        $this->confirmedSwaps = collect($this->confirmedSwaps)
            ->reject(fn ($s) => $s['swap_id'] === $swapId)
            ->values()
            ->toArray();
    }

    public function dismissAll(SwapService $swaps): void
    {
        $artist = auth()->user()->artist;

        collect($this->confirmedSwaps)->pluck('swap_id')->each(function ($id) use ($swaps, $artist) {
            $swap = Swap::find($id);
            if ($swap) {
                $swaps->markConfirmedSeen($swap, $artist->id);
            }
        });

        $this->confirmedSwaps = [];
    }

    public function goToSwaps(SwapService $swaps)
    {
        $this->dismissAll($swaps);

        return redirect()->route('swaps.index');
    }

    public function render()
    {
        return view('livewire.swap-confirmed-notification');
    }
}