<?php

namespace App\Livewire;

use App\Models\Availability;
use App\Models\Swap;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AvailabilityCalendar extends Component
{
    public $currentMonth;

    public $myAvailableDates = [];

    public $confirmedDates = [];

    public $theirAvailableDates = [];

    public $compareArtistId = null;

    public $compareArtistName = null;

    public $highlightStart = null;

    public $highlightEnd = null;

    public function mount()
    {
        $highlightStart = request()->query('highlight_start');
        $highlightEnd = request()->query('highlight_end');
        $compareArtistId = request()->query('compare_artist_id');

        if ($compareArtistId) {
            $this->compareArtistId = (int) $compareArtistId;
        }

        if ($highlightStart) {
            $this->highlightStart = $highlightStart;
            $this->highlightEnd = $highlightEnd ?: $highlightStart;
            $this->currentMonth = Carbon::parse($highlightStart)->startOfMonth();
        } else {
            $this->currentMonth = now()->startOfMonth();
        }

        $this->loadDates();
    }

    public function loadDates()
    {
        $artist = Auth::user()->artist;

        $this->myAvailableDates = Availability::where('artist_id', $artist->id)
            ->pluck('date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->toArray();

        if ($this->compareArtistId) {
            $compareArtist = \App\Models\Artist::with('user')->find($this->compareArtistId);
            $this->compareArtistName = $compareArtist?->user->name;

            $this->theirAvailableDates = Availability::where('artist_id', $this->compareArtistId)
                ->pluck('date')
                ->map(fn ($date) => $date->format('Y-m-d'))
                ->toArray();
        }

        $confirmedSwaps = Swap::where('status', 'aceptado')
            ->where(function ($query) use ($artist) {
                $query->where('artist_a_id', $artist->id)
                    ->orWhere('artist_b_id', $artist->id);
            })
            ->get();

        $dates = [];
        foreach ($confirmedSwaps as $swap) {
            $period = Carbon::parse($swap->start_date)->toPeriod($swap->end_date);
            foreach ($period as $date) {
                $dates[] = $date->format('Y-m-d');
            }
        }
        $this->confirmedDates = $dates;
    }

    public function toggleDate($date)
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

        $this->loadDates();
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