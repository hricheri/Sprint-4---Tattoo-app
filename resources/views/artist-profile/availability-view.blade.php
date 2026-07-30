<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-sans font-black text-3xl text-gray-900">{{ $artist->user->name }}'s availability</h1>
            <a href="{{ route('explore') }}"
                class="font-sans font-semibold text-sm text-gray-500 hover:text-gray-700">
                ← Back to Explore
            </a>
        </div>
        <p class="font-sans text-gray-500 mt-1">Days they've marked as free to travel.</p>
    </x-slot>

    <div class="max-w-md mx-auto py-10 px-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('artists.availability', $artist) }}?month={{ $prevMonth }}"
                    class="font-sans font-bold text-gray-500 hover:text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-100 transition">
                    ←
                </a>
                <h2 class="font-sans font-extrabold text-lg text-gray-900">{{ $month->format('F Y') }}</h2>
                <a href="{{ route('artists.availability', $artist) }}?month={{ $nextMonth }}"
                    class="font-sans font-bold text-gray-500 hover:text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-100 transition">
                    →
                </a>
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
                        $isCurrentMonth = $day->month === $month->month;
                        $isAvailable = in_array($dateStr, $availableDates);
                    @endphp

                    <div class="aspect-square rounded-xl font-sans text-sm font-semibold flex items-center justify-center
                        {{ !$isCurrentMonth ? 'text-gray-300' : 'text-gray-700' }}
                        {{ $isAvailable ? 'bg-lima-100' : '' }}
                    ">
                        {{ $day->day }}
                    </div>
                @endforeach
            </div>

            <div class="flex items-center gap-1.5 text-xs font-sans mt-6">
                <span class="w-3 h-3 rounded-full bg-lima-100"></span> Marked as available
            </div>
        </div>

        @if ($alreadyLiked)
            <div class="mt-6 bg-lima-50 border border-lima-200 rounded-2xl px-5 py-4 text-center">
                <p class="font-sans font-semibold text-sm text-lima-700">✓ You already liked {{ $artist->user->name }}</p>
            </div>
        @else
            <div class="flex gap-4 mt-6">
                <form method="POST" action="{{ route('explore.dismiss') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="artist_id" value="{{ $artist->id }}">
                    <button type="submit"
                        class="w-full font-sans font-extrabold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-2xl py-4 transition">
                        ✕ Discard
                    </button>
                </form>
                <form method="POST" action="{{ route('likes.store') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="liked_artist_id" value="{{ $artist->id }}">
                    <button type="submit"
                        class="w-full font-sans font-extrabold text-gray-900 bg-lima-300 hover:bg-lima-400 rounded-2xl py-4 transition">
                        💖 Like
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>