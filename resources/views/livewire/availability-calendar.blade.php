<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
    @if ($compareArtistId)
        <div class="mb-6 bg-lavender-50 border border-lavender-200 rounded-xl p-4">
            <p class="font-sans text-sm text-lavender-700">
                Days ringed in purple are already marked available by
                <span class="font-bold">{{ $compareArtistName ?? 'this artist' }}</span>.
                @if ($highlightStart)
                    Try to match your availability with the requested range:
                    <span class="font-bold">{{ \Carbon\Carbon::parse($highlightStart)->format('M j') }}</span>
                    –
                    <span class="font-bold">{{ \Carbon\Carbon::parse($highlightEnd)->format('M j, Y') }}</span>.
                @endif
            </p>
        </div>
    @endif

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
            @endphp

            <button
                @if (!$isPast && !$isConfirmed) wire:click="toggleDate('{{ $dateStr }}')" @endif
                @disabled($isPast || $isConfirmed)
                class="aspect-square rounded-xl font-sans text-sm font-semibold transition relative
                    {{ !$isCurrentMonth ? 'text-gray-300' : 'text-gray-700' }}
                    {{ $isPast ? 'opacity-30 cursor-not-allowed' : '' }}
                    {{ $isConfirmed ? 'bg-lavender-300 text-gray-900 cursor-not-allowed' : '' }}
                    {{ $isAvailable && !$isConfirmed ? 'bg-lima-300 text-gray-900' : '' }}
                    {{ $isTheirs && !$isConfirmed ? 'ring-2 ring-lavender-700' : '' }}
                    {{ !$isAvailable && !$isConfirmed && !$isPast ? 'hover:bg-gray-100 border border-gray-200' : '' }}
                ">
                {{ $day->day }}
            </button>
        @endforeach
    </div>

    <div class="flex items-center justify-between mt-6">
        <div class="flex gap-4 text-xs font-sans flex-wrap">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-lima-300"></span> Available
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-lavender-300"></span> Confirmed swap
            </div>
            @if ($compareArtistId)
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full ring-2 ring-lavender-700"></span> Their availability
                </div>
            @endif
        </div>

        <a href="{{ route('explore') }}"
            class="font-sans font-extrabold text-sm bg-gray-900 hover:bg-gray-800 text-white rounded-full px-5 py-2 transition">
            Done
        </a>
    </div>
</div>