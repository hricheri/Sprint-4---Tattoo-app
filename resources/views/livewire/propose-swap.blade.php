<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
    <div class="flex items-center gap-3 mb-6">
        @if ($artist->profile_photo)
            <img src="{{ photo_url($artist->profile_photo) }}" class="w-12 h-12 rounded-full object-cover">
        @endif
        <div>
            <p class="font-sans font-bold text-gray-900">{{ $artist->user->name }}</p>
            <p class="font-sans text-sm text-lavender-600">{{ $artist->studio->city }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
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
                $theirsAvailable = in_array($dateStr, $theirAvailableDates);
                $inRange = $selectedStart && $selectedEnd && $dateStr >= $selectedStart && $dateStr <= $selectedEnd;
                $isEdge = $dateStr === $selectedStart || $dateStr === $selectedEnd;
            @endphp

            <button
                @if (!$isPast) wire:click="selectDate('{{ $dateStr }}')" @endif
                @disabled($isPast)
                class="aspect-square rounded-xl font-sans text-sm font-semibold transition relative
                    {{ !$isCurrentMonth ? 'text-gray-300' : 'text-gray-700' }}
                    {{ $isPast ? 'opacity-30 cursor-not-allowed' : '' }}
                    {{ $isEdge ? 'bg-lavender-500 text-white' : '' }}
                    {{ $inRange && !$isEdge ? 'bg-lavender-100' : '' }}
                    {{ $theirsAvailable && !$inRange && !$isEdge ? 'ring-2 ring-lima-300' : '' }}
                    {{ !$inRange && !$isEdge && !$isPast ? 'hover:bg-gray-100 border border-gray-200' : '' }}
                ">
                {{ $day->day }}
            </button>
        @endforeach
    </div>

    <div class="flex items-center gap-4 mt-6 text-xs font-sans">
        <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full ring-2 ring-lima-300"></span> Their availability
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-lavender-500"></span> Your selection
        </div>
    </div>

    <div class="mt-6 pt-6 border-t border-gray-100">
        <p class="font-sans text-sm text-gray-600 mb-4">
            @if ($selectedStart && $selectedEnd)
                Selected: {{ \Carbon\Carbon::parse($selectedStart)->format('M j') }} – {{ \Carbon\Carbon::parse($selectedEnd)->format('M j, Y') }}
            @else
                Click a start date, then an end date.
            @endif
        </p>

        <label class="flex items-center gap-2 cursor-pointer mb-4">
            <input type="checkbox" wire:model="includesMoneyExchange" class="rounded text-lavender-500 focus:ring-lavender-400">
            <span class="font-sans text-sm text-gray-700">This swap includes a money exchange</span>
        </label>

        @error('selectedStart') <p class="text-red-500 text-sm mb-2">{{ $message }}</p> @enderror
        @error('selectedEnd') <p class="text-red-500 text-sm mb-2">{{ $message }}</p> @enderror

        <button wire:click="submit"
            class="w-full font-sans font-extrabold text-lg bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-2xl py-4 shadow-sm transition">
            Send Proposal
        </button>
    </div>
</div>