<?php

namespace App\Livewire;

use App\Models\Artist;
use App\Models\Availability;
use App\Models\Swap;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProposeSwap extends Component
{
    public Artist $artist;

    public $currentMonth;

    public $theirAvailableDates = [];

    public $selectedStart = null;

    public $selectedEnd = null;

    public $includesMoneyExchange = false;

    public function mount(Artist $artist)
    {
        $this->artist = $artist;
        $this->currentMonth = now()->startOfMonth();
        $this->loadTheirDates();
    }

    public function loadTheirDates()
    {
        $this->theirAvailableDates = Availability::where('artist_id', $this->artist->id)
            ->pluck('date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->toArray();
    }

    public function selectDate($date)
    {
        if (!$this->selectedStart || $this->selectedEnd) {
            $this->selectedStart = $date;
            $this->selectedEnd = null;
            return;
        }

        if ($date < $this->selectedStart) {
            $this->selectedEnd = $this->selectedStart;
            $this->selectedStart = $date;
        } else {
            $this->selectedEnd = $date;
        }
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

    public function submit()
    {
        $this->validate([
            'selectedStart' => 'required|date',
            'selectedEnd' => 'required|date|after:selectedStart',
        ]);

        $myArtist = Auth::user()->artist;

        Swap::create([
            'artist_a_id' => $myArtist->id,
            'artist_b_id' => $this->artist->id,
            'start_date' => $this->selectedStart,
            'end_date' => $this->selectedEnd,
            'status' => 'pendiente',
            'includes_money_exchange' => $this->includesMoneyExchange,
        ]);

        session()->flash('status', 'Swap proposal sent!');

        return redirect()->route('swaps.index');
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

        return view('livewire.propose-swap', [
            'days' => $days,
            'canGoBack' => $canGoBack,
            'canGoForward' => $canGoForward,
        ]);
    }
}