<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
    <div class="flex items-center justify-between mb-6">
        <button wire:click="previousMonth" @disabled(!$canGoBack)
            class="font-sans font-bold text-gray-500 hover:text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-100 transition disabled:opacity-30 disabled:cursor-not-allowed">
            ←
        </button>
        <h2 class="font-sans font-extrabold text-lg text-gray-900">{{ $currentMonth->format('F Y') }}</h2>
        <button wire:click="nextMonth" @disabled(!$canGoForward)
            class="font-sans font-bold text-gray-500 hover:text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-100 transition disabled:opacity-30 disabled:cursor-not-allowed">
            →
        </button>
    </div>

    <div class="grid grid-cols-7 gap-2 mb-2">
        @foreach (['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'] as $label)
            <div class="font-sans font-bold text-xs text-center text-gray-400">{{ $label }}</div>
        @endforeach
    </div>

    <div class="grid grid-cols-7 gap-2">
        @foreach ($days as $day)
            @php
                $dateStr = $day->format('Y-m-d');
                $isCurrentMonth = $day->month === $currentMonth->month;
                $isPast = $day->isBefore(now()->startOfDay());
                $isConfirmed = in_array($dateStr, $confirmedDates);
                $isAvailable = in_array($dateStr, $myAvailableDates);
                $isTheirs = $compareArtistId && in_array($dateStr, $theirAvailableDates);
                $isOverlap = $isAvailable && $isTheirs;
                $isMineOnly = $isAvailable && !$isTheirs;
                $isTheirsOnly = $isTheirs && !$isAvailable;
                $confirmedInfo = $confirmedSwapByDate[$dateStr] ?? null;
            @endphp

            @if ($isConfirmed && $confirmedInfo)
                <a href="{{ route('swaps.index') }}"
                    title="Going to {{ $confirmedInfo['city'] }} — click for details"
                    class="aspect-square rounded-xl font-sans text-sm font-semibold transition relative flex items-center justify-center bg-lavender-500 text-white hover:bg-lavender-600 group">
                    {{ $day->day }}
                    <span class="pointer-events-none absolute bottom-full mb-1 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-900 text-white text-xs px-2 py-1 opacity-0 group-hover:opacity-100 transition z-10">
                        Going to {{ $confirmedInfo['city'] }}
                    </span>
                </a>
            @else
                <button
                    @if (!$isPast) wire:click="toggleDate('{{ $dateStr }}')" @endif
                    @disabled($isPast)
                    class="aspect-square rounded-xl font-sans text-sm font-semibold transition relative
                        {{ $isOverlap ? 'bg-lavender-100' : '' }}
                        {{ $isMineOnly ? 'bg-lima-100' : '' }}
                        {{ $isTheirsOnly ? 'ring-2 ring-lavender-500' : '' }}
                        {{ !$isCurrentMonth ? 'text-gray-300' : 'text-gray-700' }}
                        {{ $isPast ? 'opacity-30 cursor-not-allowed' : '' }}
                        {{ !$isAvailable && !$isTheirs && !$isPast ? 'hover:bg-gray-100' : '' }}
                    ">
                    {{ $day->day }}
                </button>
            @endif
        @endforeach
    </div>

    <div class="flex items-center gap-4 text-xs font-sans flex-wrap mt-6">
        <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-lima-100"></span> My availability
        </div>
        @if ($compareArtistId)
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full ring-2 ring-lavender-500"></span> Their availability
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-lavender-100"></span> Possible swap dates
            </div>
        @endif
        @if (count($confirmedDates) > 0)
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-lavender-500"></span> Confirmed swap
            </div>
        @endif
    </div>

    @if ($swap)
        @php
            $myArtistId = auth()->user()->artist->id;
            $iConfirmed = $swap->myConfirmed($myArtistId);
        @endphp

        <div class="mt-6 rounded-xl p-4 bg-gray-50 border border-gray-200">
            @if ($swap->isAwaitingAvailability())
                <p class="font-sans text-sm text-gray-700">
                    First, mark your general availability. Days ringed in purple are already marked available by
                    <span class="font-bold">{{ $compareArtistName }}</span> — try to overlap with those.
                </p>
            @elseif ($swap->isAwaitingConfirmation())
                <p class="font-sans font-bold text-gray-900 mb-1">
                    ✓ Overlapping dates: {{ $swap->start_date->format('M j') }} – {{ $swap->end_date->format('M j, Y') }}
                </p>
                <p class="font-sans text-sm text-gray-600 mb-3">
                    @if ($iConfirmed)
                        You've selected these as your final dates for this swap. Waiting for {{ $compareArtistName }} to confirm the same range.
                    @else
                        These are the days where your general availability overlaps with {{ $compareArtistName }}'s. If you'd rather swap
                        for a shorter stretch, untoggle the days you don't want first — only what's left highlighted will be sent to
                        {{ $compareArtistName }} for their confirmation.
                    @endif
                </p>
                @unless ($iConfirmed)
                    <div class="flex gap-2">
                        <button wire:click="confirmSwapDates" class="font-sans font-extrabold text-sm bg-lavender-500 hover:bg-lavender-600 text-white rounded-full px-5 py-2 transition">
                            Confirm these dates
                        </button>
                        <button wire:click="declineSwap" class="font-sans font-semibold text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full px-5 py-2 transition">
                            Decline
                        </button>
                    </div>
                @endunless
            @elseif ($swap->status === 'aceptado')
                <p class="font-sans font-bold text-gray-900">
                    ✈️ This swap is confirmed: {{ $swap->start_date->format('M j') }} – {{ $swap->end_date->format('M j, Y') }}
                </p>
            @endif
        </div>
    @endif

    <div class="flex justify-end mt-6">
        @unless ($swap && !$swap->isAwaitingAvailability())
            <a href="{{ route('swaps.index') }}"
                class="font-sans font-extrabold text-sm bg-gray-900 hover:bg-gray-800 text-white rounded-full px-5 py-2 transition whitespace-nowrap">
                {{ $swap ? 'Send' : 'Done' }}
            </a>
        @endunless
    </div>
</div>