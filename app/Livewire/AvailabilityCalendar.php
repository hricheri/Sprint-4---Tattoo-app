<?php

namespace App\Livewire;

use App\Models\Artist;
use App\Models\Availability;
use App\Models\Swap;
use App\Services\SwapService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AvailabilityCalendar extends Component
{
    public $currentMonth;

    public $myAvailableDates = [];

    public $confirmedDates = [];

    public $confirmedSwapByDate = [];

    public $theirAvailableDates = [];

    public $swapId = null;

    public $swap = null;

    public $compareArtistId = null;

    public $compareArtistName = null;

    public function mount()
    {
        $swapId = request()->query('swap_id');

        if ($swapId) {
            $this->loadSwapContext((int) $swapId);
        }

        $this->currentMonth = $this->swap?->start_date
            ? $this->swap->start_date->copy()->startOfMonth()
            : now()->startOfMonth();

        $this->loadDates();
    }

    private function loadSwapContext(int $swapId): void
    {
        $artist = Auth::user()->artist;
        $swap = Swap::find($swapId);

        if (! $swap || ($swap->artist_a_id !== $artist->id && $swap->artist_b_id !== $artist->id)) {
            return;
        }

        $this->swapId = $swap->id;
        $this->swap = $swap;

        // Una vez confirmado, ya no tiene sentido comparar "mío vs suyo":
        // solo mostramos el bloque final de "confirmed swap".
        if ($swap->status !== 'aceptado') {
            $this->compareArtistId = $swap->otherArtistId($artist->id);

            $compareArtist = Artist::with('user')->find($this->compareArtistId);
            $this->compareArtistName = $compareArtist?->user->name;
        }
    }

    public function loadDates()
    {
        $artist = Auth::user()->artist;

        $this->myAvailableDates = Availability::where('artist_id', $artist->id)
            ->pluck('date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->toArray();

        if ($this->compareArtistId) {
            $this->theirAvailableDates = Availability::where('artist_id', $this->compareArtistId)
                ->pluck('date')
                ->map(fn ($date) => $date->format('Y-m-d'))
                ->toArray();
        }

        $confirmedSwaps = Swap::where('status', 'aceptado')
            ->with(['artistA.studio', 'artistB.studio'])
            ->where(function ($query) use ($artist) {
                $query->where('artist_a_id', $artist->id)
                    ->orWhere('artist_b_id', $artist->id);
            })
            ->get();

        $dates = [];
        $byDate = [];

        foreach ($confirmedSwaps as $confirmedSwap) {
            $otherArtist = $confirmedSwap->artist_a_id === $artist->id
                ? $confirmedSwap->artistB
                : $confirmedSwap->artistA;

            $period = Carbon::parse($confirmedSwap->start_date)->toPeriod($confirmedSwap->end_date);
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $dates[] = $key;
                $byDate[$key] = [
                    'swap_id' => $confirmedSwap->id,
                    'city' => $otherArtist->studio->city ?? '',
                ];
            }
        }

        $this->confirmedDates = $dates;
        $this->confirmedSwapByDate = $byDate;
    }

    public function toggleDate($date, SwapService $swaps)
    {
        if (in_array($date, $this->confirmedDates)) {
            return;
        }

        $artist = Auth::user()->artist;

        $existing = Availability::where('artist_id', $artist->id)
            ->where('date', $date)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Availability::create([
                'artist_id' => $artist->id,
                'date' => $date,
            ]);
        }

        $swaps->recalculateAllFor($artist->id);

        if ($this->swapId) {
            $this->swap = Swap::find($this->swapId);
        }

        $this->loadDates();
    }

    public function confirmSwapDates(SwapService $swaps)
    {
        $myArtist = Auth::user()->artist;
        $swap = Swap::find($this->swapId);

        abort_unless($swap && ($swap->artist_a_id === $myArtist->id || $swap->artist_b_id === $myArtist->id), 403);

        $swaps->confirm($swap, $myArtist->id);

        return redirect()->route('swaps.index')->with('status',
            $swap->fresh()->status === 'aceptado'
                ? 'Swap confirmed! Check your travel calendar below.'
                : 'Dates confirmed on your side — waiting for the other artist to confirm.'
        );
    }

    public function declineSwap(SwapService $swaps)
    {
        $swap = Swap::find($this->swapId);

        if ($swap) {
            $swaps->reject($swap);
        }

        return redirect()->route('swaps.index')->with('status', 'Swap declined.');
    }

    public function previousMonth()
    {
        if ($this->currentMonth->gt(now()->startOfMonth())) {
            $this->currentMonth = $this->currentMonth->copy()->subMonth();
        }
    }

    public function nextMonth()
    {
        $maxMonth = now()->startOfMonth()->addMonths(12);

        if ($this->currentMonth->lt($maxMonth)) {
            $this->currentMonth = $this->currentMonth->copy()->addMonth();
        }
    }

    public function render()
    {
        $startOfMonth = $this->currentMonth->copy()->startOfMonth();
        $endOfMonth = $this->currentMonth->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        $date = $startOfCalendar->copy();
        while ($date->lte($endOfCalendar)) {
            $days[] = $date->copy();
            $date->addDay();
        }

        $canGoBack = $this->currentMonth->gt(now()->startOfMonth());
        $canGoForward = $this->currentMonth->lt(now()->startOfMonth()->addMonths(12));

        return view('livewire.availability-calendar', [
            'days' => $days,
            'canGoBack' => $canGoBack,
            'canGoForward' => $canGoForward,
        ]);
    }
}