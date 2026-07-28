<x-app-layout>
    <x-slot name="header">
        <h1 class="font-sans font-black text-3xl text-gray-900">My Swaps</h1>
        <p class="font-sans text-gray-500 mt-1">Manage your matches and confirmed exchanges.</p>
    </x-slot>

    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-10">

        @if (session('status'))
            <div class="bg-lima-100 text-lima-800 font-sans font-semibold rounded-2xl px-6 py-4">
                {{ session('status') }}
            </div>
        @endif

        {{-- === NEW MATCHES === --}}
        @if ($newMatches->isNotEmpty())
            <div>
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-4">🎉 New matches</h2>
                <div class="space-y-3">
                    @foreach ($newMatches as $artist)
                        <div class="bg-white rounded-2xl shadow-sm border border-lavender-200 p-5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if ($artist->profile_photo)
                                    <img src="{{ photo_url($artist->profile_photo) }}" class="w-12 h-12 rounded-full object-cover">
                                @endif
                                <div>
                                    <p class="font-sans font-bold text-gray-900">{{ $artist->user->name }}</p>
                                    <p class="font-sans text-sm text-gray-500">You both liked each other!</p>
                                    <a href="{{ route('artists.show', $artist) }}" class="font-sans text-xs text-lavender-600 hover:underline">View full profile →</a>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('swaps.start', $artist) }}">
                                @csrf
                                <button type="submit" class="font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full px-5 py-2 transition whitespace-nowrap">
                                    Set Dates
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- === AWAITING AVAILABILITY === --}}
        @if ($awaitingAvailability->isNotEmpty())
            <div>
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-4">📆 Waiting for matching dates</h2>
                <div class="space-y-3">
                    @foreach ($awaitingAvailability as $swap)
                        @php $other = $swap->artist_a_id === $myArtist->id ? $swap->artistB : $swap->artistA; @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if ($other->profile_photo)
                                    <img src="{{ photo_url($other->profile_photo) }}" class="w-12 h-12 rounded-full object-cover">
                                @endif
                                <div>
                                    <p class="font-sans font-bold text-gray-900">{{ $other->user->name }}</p>
                                    @if ($hasSetAvailability)
                                        <p class="font-sans text-sm text-gray-500">You've marked your availability — waiting to find overlapping dates.</p>
                                    @else
                                        <p class="font-sans text-sm text-gray-500">Mark your availability to find matching dates.</p>
                                    @endif
                                </div>
                            </div>
                            @if ($hasSetAvailability)
                                <a href="{{ route('availability') }}?swap_id={{ $swap->id }}"
                                    class="font-sans font-bold text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full px-5 py-2 transition whitespace-nowrap inline-flex items-center gap-1">
                                    Edit my availability →
                                </a>
                            @else
                                <a href="{{ route('availability') }}?swap_id={{ $swap->id }}"
                                    class="font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full px-5 py-2 transition whitespace-nowrap inline-flex items-center gap-1">
                                    Set my availability →
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- === AWAITING CONFIRMATION === --}}
        @if ($awaitingConfirmation->isNotEmpty())
            <div>
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-4">✅ Confirm your dates</h2>
                <div class="space-y-3">
                    @foreach ($awaitingConfirmation as $swap)
                        @php
                            $other = $swap->artist_a_id === $myArtist->id ? $swap->artistB : $swap->artistA;
                            $iConfirmed = $swap->myConfirmed($myArtist->id);
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @if ($other->profile_photo)
                                        <img src="{{ photo_url($other->profile_photo) }}" class="w-12 h-12 rounded-full object-cover">
                                    @endif
                                    <div>
                                        <p class="font-sans font-bold text-gray-900">{{ $other->user->name }}</p>
                                        <p class="font-sans text-sm text-gray-500">{{ $swap->start_date->format('M j') }} – {{ $swap->end_date->format('M j, Y') }}</p>
                                    </div>
                                </div>

                                @if ($iConfirmed)
                                    <span class="font-sans font-bold text-xs bg-gray-100 text-gray-500 rounded-full px-3 py-1.5 whitespace-nowrap">
                                        Waiting for {{ $other->user->name }} to confirm
                                    </span>
                                @else
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('swaps.reject', $swap) }}">
                                            @csrf
                                            <button class="font-sans font-semibold text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full px-4 py-2 transition">
                                                Decline
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('swaps.confirm-dates', $swap) }}">
                                            @csrf
                                            <button class="font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full px-4 py-2 transition">
                                                Confirm
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <a href="{{ route('artists.show', $other) }}"
                                    class="font-sans font-semibold text-sm text-lavender-600 hover:underline">
                                    View full profile →
                                </a>
                                <a href="{{ route('availability') }}?swap_id={{ $swap->id }}"
                                    class="font-sans text-xs text-lavender-600 hover:underline">
                                    View on calendar →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- === CONFIRMED SWAPS (travel calendar) === --}}
        <div>
            <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-4">📅 Confirmed swaps</h2>

            @if ($confirmed->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                    <p class="font-sans text-sm text-gray-400">No confirmed swaps yet.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($confirmed as $swap)
                        @php
                            $otherArtist = $swap->artist_a_id === $myArtist->id ? $swap->artistB : $swap->artistA;
                            $iSentPromo = $swap->promoSentFor($myArtist->id);
                            $bothSent = $swap->bothPromosSent();
                            $urgent = $swap->isPromoReminderUrgent() && !$iSentPromo;
                        @endphp
                        <div x-data="{ open: false }" class="bg-lavender-50 border-2 border-lavender-300 rounded-2xl p-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-sans font-black text-lg text-gray-900">
                                        ✈️ You're going to {{ $otherArtist->studio->city }}!
                                    </p>
                                    <p class="font-sans text-sm text-gray-600">
                                        {{ $swap->start_date->format('M j') }} – {{ $swap->end_date->format('M j, Y') }} with {{ $otherArtist->user->name }}
                                    </p>
                                </div>
                                <button @click="open = !open"
                                    class="font-sans font-extrabold text-sm bg-lavender-300 hover:bg-lavender-400 text-gray-900 rounded-full px-5 py-2 transition shrink-0 whitespace-nowrap">
                                    <span x-show="!open">View details</span>
                                    <span x-show="open">Hide details</span>
                                </button>
                            </div>

                            {{-- Promo status — always visible, not hidden behind accordion --}}
                            <div class="mt-4">
                                @if ($bothSent)
                                    <span class="font-sans font-bold text-xs bg-lima-100 text-lima-700 rounded-full px-3 py-1.5 inline-block">
                                        ✓ Both guest announcements sent
                                    </span>
                                @elseif ($iSentPromo)
                                    <span class="font-sans font-bold text-xs bg-gray-100 text-gray-500 rounded-full px-3 py-1.5 inline-block">
                                        You sent yours — waiting for {{ $otherArtist->user->name }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('swaps.promo-sent', $swap) }}">
                                        @csrf
                                        <button class="font-sans font-extrabold text-sm rounded-full px-4 py-2 transition
                                            {{ $urgent ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-lima-300 hover:bg-lima-400 text-gray-900' }}">
                                            {{ $urgent ? '⏰ Reminder: send your guest announcement' : '📸 Send your guest announcement' }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div x-show="open" class="mt-5 pt-5 border-t border-lavender-200 space-y-4">
                                @if ($bothSent)
                                    <a href="{{ route('artists.show', $otherArtist) }}" class="inline-block font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full px-5 py-2 transition">
                                        View full profile, photos & materials →
                                    </a>

                                    <div>
                                        <p class="font-sans font-bold text-xs uppercase tracking-wide text-gray-500 mb-1">Their studio</p>
                                        <p class="font-sans text-sm text-gray-700">{{ $otherArtist->studio->address }}</p>
                                        <p class="font-sans text-sm text-gray-600">{{ $otherArtist->studio->access_instructions }}</p>
                                    </div>
                                    <div>
                                        <p class="font-sans font-bold text-xs uppercase tracking-wide text-gray-500 mb-1">Their home</p>
                                        <p class="font-sans text-sm text-gray-600">{{ $otherArtist->home->access_instructions }}</p>
                                    </div>
                                    <div>
                                        <p class="font-sans font-bold text-xs uppercase tracking-wide text-gray-500 mb-1">Contact</p>
                                        <p class="font-sans text-sm text-gray-700">{{ $otherArtist->contact_email }} · {{ $otherArtist->social_media_handle }}</p>
                                    </div>
                                @else
                                    <p class="font-sans text-sm text-gray-500">
                                        Address and access instructions unlock once both of you send your guest announcements.
                                    </p>
                                @endif

                                <div class="bg-white rounded-xl p-4">
                                    <p class="font-sans font-bold text-xs uppercase tracking-wide text-lavender-600 mb-1">How this works</p>
                                    <p class="font-sans text-sm text-gray-600">
                                        Send {{ $otherArtist->user->name }} a promo graphic announcing you as their guest artist
                                        in {{ $otherArtist->studio->city }}, so they can share it on their studio's social media —
                                        that way their local followers know to book with you while you're in town. Once you both
                                        send yours, you'll each get the other's exact address and access instructions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>