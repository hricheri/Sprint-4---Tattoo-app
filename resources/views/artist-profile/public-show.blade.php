<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-sans font-black text-3xl text-gray-900">{{ $artist->user->name }}</h1>
            <a href="{{ url()->previous() }}"
                class="font-sans font-semibold text-sm text-gray-500 hover:text-gray-700">
                ← Back
            </a>
        </div>
        <p class="font-sans text-gray-500 mt-1">Full profile</p>
    </x-slot>

    <div class="max-w-sm mx-auto py-10 px-4">
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">

            <div class="relative h-48 bg-gray-100">
                @if ($artist->studio->photo)
                    <img src="{{ photo_url($artist->studio->photo) }}" alt="Studio" class="w-full h-full object-cover">
                @endif
                <span class="absolute top-3 left-3 font-sans font-extrabold text-xs uppercase tracking-wide bg-lima-300 text-gray-900 rounded-full px-3 py-1">
                    Studio
                </span>
            </div>

            <div class="relative h-6 -mt-6">
                <svg viewBox="0 0 100 20" preserveAspectRatio="none" class="w-full h-6">
                    <path d="M0,0 C50,20 50,20 100,0 L100,20 L0,20 Z" fill="white" />
                </svg>
            </div>

            <div class="relative h-48 bg-gray-100 -mt-6">
                @if ($artist->home->photo)
                    <img src="{{ photo_url($artist->home->photo) }}" alt="Home" class="w-full h-full object-cover">
                @endif
                <span class="absolute top-3 left-3 font-sans font-extrabold text-xs uppercase tracking-wide bg-lavender-300 text-gray-900 rounded-full px-3 py-1">
                    Home
                </span>
            </div>

            <div class="p-6">
                <div class="flex items-center gap-3">
                    @if ($artist->profile_photo)
                        <img src="{{ photo_url($artist->profile_photo) }}" alt="{{ $artist->user->name }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow -mt-12">
                    @endif
                    <div>
                        <h2 class="font-sans font-black text-2xl text-gray-900">{{ $artist->user->name }}</h2>
                        <p class="font-sans font-semibold text-lavender-600">{{ $artist->studio->city }}</p>
                    </div>
                </div>

                @if ($artist->bio)
                    <p class="font-sans text-sm text-gray-600 mt-3">{{ $artist->bio }}</p>
                @endif

                <div class="mt-4">
                    <p class="font-sans font-bold text-xs uppercase tracking-wide text-gray-500 mb-1.5">Studio offers</p>
                    <div class="flex flex-wrap gap-1.5">
                        @if ($offersAllMustHaves)
                            <span class="font-sans font-bold text-xs bg-lima-300 text-gray-900 rounded-full px-3 py-1">
                                ✓ All must-haves
                            </span>
                        @endif
                        @foreach ($artist->studio->features as $feature)
                            <span class="font-sans text-xs bg-lima-100 text-lima-700 rounded-full px-3 py-1">{{ $feature->name }}</span>
                        @endforeach
                    </div>
                </div>

                @if ($artist->featurePreferences->isNotEmpty())
                    <div class="mt-4">
                        <p class="font-sans font-bold text-xs uppercase tracking-wide text-gray-500 mb-1.5">Looking for</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($artist->featurePreferences as $feature)
                                <span class="font-sans text-xs bg-lavender-100 text-lavender-700 rounded-full px-3 py-1">{{ $feature->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-5 pt-5 border-t border-gray-100">
                    <p class="font-sans font-bold text-xs uppercase tracking-wide text-gray-500 mb-2">Home</p>
                    <ul class="font-sans text-sm text-gray-600 space-y-1">
                        <li>{{ $artist->home->roommates_count }} roommate(s)</li>
                        @if ($artist->home->distance_to_studio_minutes !== null)
                            <li>{{ $artist->home->distance_to_studio_minutes }} min to studio ({{ str_replace('_', ' ', $artist->home->transport_type) }})</li>
                        @endif
                    </ul>
                </div>

                @if ($canSeeSensitiveInfo)
                    <div class="mt-5 pt-5 border-t border-gray-100 bg-lima-50 rounded-xl p-4 -mx-2">
                        <p class="font-sans font-bold text-xs uppercase tracking-wide text-lima-700 mb-2">✓ Swap confirmed — access details</p>
                        <p class="font-sans text-sm text-gray-700 font-semibold">Studio address</p>
                        <p class="font-sans text-sm text-gray-600 mb-2">{{ $artist->studio->address }}</p>
                        <p class="font-sans text-sm text-gray-600 mb-3">{{ $artist->studio->access_instructions }}</p>
                        <p class="font-sans text-sm text-gray-700 font-semibold">Home access</p>
                        <p class="font-sans text-sm text-gray-600 mb-3">{{ $artist->home->access_instructions }}</p>
                        <p class="font-sans text-sm text-gray-700 font-semibold">Contact</p>
                        <p class="font-sans text-sm text-gray-600">{{ $artist->contact_email }} · {{ $artist->social_media_handle }}</p>
                    </div>
                @else
                    <p class="font-sans text-xs text-gray-400 mt-4">
                        Exact address and access instructions are shared once both guest announcements are sent.
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>