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
                                </div>
                            </div>
                            <a href="{{ route('swaps.create', $artist) }}"
                                class="font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full px-5 py-2 transition">
                                Propose Swap
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
                                        Once your session with a guest client is done, send a photo of the finished tattoo to
                                        {{ $otherArtist->user->name }} ({{ $otherArtist->contact_email }}) within 48 hours, so
                                        they can post it on their studio's social media. Do the same for any work they send you.
                                        If you need anything during your stay, reach out via the contact info above.
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