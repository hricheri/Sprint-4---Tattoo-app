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
                            <a href="{{ route('swaps.create', $artist) }}"
                                class="font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full px-5 py-2 transition">
                                Set Dates
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- === RECEIVED PROPOSALS === --}}
        @if ($pendingReceived->isNotEmpty())
            <div>
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-4">Swap requests for you</h2>
                <div class="space-y-3">
                    @foreach ($pendingReceived as $swap)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @if ($swap->artistA->profile_photo)
                                        <img src="{{ photo_url($swap->artistA->profile_photo) }}" class="w-12 h-12 rounded-full object-cover">
                                    @endif
                                    <div>
                                        <p class="font-sans font-bold text-gray-900">{{ $swap->artistA->user->name }}</p>
                                        <p class="font-sans text-sm text-gray-500">{{ $swap->start_date->format('M j') }} – {{ $swap->end_date->format('M j, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('swaps.reject', $swap) }}">
                                        @csrf
                                        <button class="font-sans font-semibold text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full px-4 py-2 transition">
                                            Decline
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('swaps.accept', $swap) }}">
                                        @csrf
                                        <button class="font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full px-4 py-2 transition">
                                            Accept
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
                                <a href="{{ route('artists.show', $swap->artistA) }}"
                                    class="font-sans font-semibold text-sm text-lavender-600 hover:underline">
                                    View full profile →
                                </a>

                                <a href="{{ route('availability') }}?highlight_start={{ $swap->start_date->format('Y-m-d') }}&highlight_end={{ $swap->end_date->format('Y-m-d') }}&compare_artist_id={{ $swap->artistA->id }}"
                                    class="font-sans font-bold text-xs rounded-full px-3 py-1.5 transition
                                        {{ $swap->available_days === $swap->total_days ? 'bg-lima-300 text-gray-900 hover:bg-lima-400' : '' }}
                                        {{ $swap->available_days > 0 && $swap->available_days < $swap->total_days ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : '' }}
                                        {{ $swap->available_days === 0 ? 'bg-red-50 text-red-600 hover:bg-red-100' : '' }}">
                                    @if ($swap->available_days === $swap->total_days)
                                        ✓ All {{ $swap->total_days }} days already match — view calendar →
                                    @elseif ($swap->available_days > 0)
                                        {{ $swap->available_days }} of {{ $swap->total_days }} days already match — view which ones →
                                    @else
                                        ⚠ No days match yet — mark your availability →
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- === SENT PROPOSALS === --}}
        @if ($pendingSent->isNotEmpty())
            <div>
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-4">Sent requests</h2>
                <div class="space-y-3">
                    @foreach ($pendingSent as $swap)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-3">
                            @if ($swap->artistB->profile_photo)
                                <img src="{{ photo_url($swap->artistB->profile_photo) }}" class="w-12 h-12 rounded-full object-cover">
                            @endif
                            <div>
                                <p class="font-sans font-bold text-gray-900">{{ $swap->artistB->user->name }}</p>
                                <p class="font-sans text-sm text-gray-500">{{ $swap->start_date->format('M j') }} – {{ $swap->end_date->format('M j, Y') }} · Waiting for response</p>
                                <div class="flex gap-3 mt-1">
                                    <a href="{{ route('artists.show', $swap->artistB) }}" class="font-sans text-xs text-lavender-600 hover:underline">View full profile →</a>
                                    <a href="{{ route('availability') }}?highlight_start={{ $swap->start_date->format('Y-m-d') }}&highlight_end={{ $swap->end_date->format('Y-m-d') }}&compare_artist_id={{ $swap->artistB->id }}" class="font-sans text-xs text-lavender-600 hover:underline">Compare availability →</a>
                                </div>
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
                        @endphp
                        <div x-data="{ open: false }" class="bg-lima-50 border-2 border-lima-300 rounded-2xl p-5">
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
                                    class="font-sans font-extrabold text-sm bg-lavender-300 hover:bg-lavender-400 text-gray-900 rounded-full px-5 py-2 transition shrink-0">
                                    <span x-show="!open">View details</span>
                                    <span x-show="open">Hide details</span>
                                </button>
                            </div>

                            <div x-show="open" class="mt-5 pt-5 border-t border-lima-200 space-y-4">
                                <a href="{{ route('artists.show', $otherArtist) }}" class="inline-block font-sans font-extrabold text-sm bg-lavender-500 hover:bg-lavender-600 text-white rounded-full px-5 py-2 transition">
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
                                <div class="bg-white rounded-xl p-4">
                                    <p class="font-sans font-bold text-xs uppercase tracking-wide text-lavender-600 mb-1">How this works</p>
                                    <p class="font-sans text-sm text-gray-600">
                                        Before your stay, send {{ $otherArtist->user->name }} a promo graphic announcing you as
                                        their guest artist in {{ $otherArtist->studio->city }}, so they can share it on their
                                        studio's social media — that way their local followers know to book with you while
                                        you're in town. Do the same for the graphic they send you. If you need anything during
                                        your stay, reach out via the contact info above.
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