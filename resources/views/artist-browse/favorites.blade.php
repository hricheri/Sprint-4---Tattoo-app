<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-sans font-black text-3xl text-gray-900">Favorites</h1>
            <a href="{{ route('explore') }}"
                class="font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full px-5 py-2 transition">
                ← Explore
            </a>
        </div>
        <p class="font-sans text-gray-500 mt-1">Artists you're interested in swapping with.</p>
    </x-slot>

    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        @if ($likes->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
                <p class="font-sans font-semibold text-gray-500">You haven't liked anyone yet.</p>
                <p class="font-sans text-sm text-gray-400 mt-1">Head to Explore to find artists to swap with.</p>
            </div>
        @else
            <div class="grid sm:grid-cols-2 gap-6">
                @foreach ($likes as $like)
                    @php
                        $artist = $like->liked;
                        $theirFeatureIds = $artist->studio->features->pluck('id')->toArray();
                        $missingIds = array_diff($myPreferenceIds, $theirFeatureIds);
                        $extraFeatures = $artist->studio->features->whereNotIn('id', $myPreferenceIds);
                    @endphp
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

                        <div class="relative h-36 bg-gray-100">
                            @if ($artist->studio->photo)
                                <img src="{{ photo_url($artist->studio->photo) }}" alt="Studio" class="w-full h-full object-cover">
                            @endif
                            @if ($matchedArtistIds->contains($artist->id))
                                <span class="absolute top-3 right-3 font-sans font-extrabold text-xs uppercase tracking-wide bg-lavender-300 text-gray-900 rounded-full px-3 py-1">
                                    🎨 Match!
                                </span>
                            @endif
                        </div>

                        <div class="p-5">
                            <div class="flex items-center gap-2">
                                @if ($artist->profile_photo)
                                    <img src="{{ photo_url($artist->profile_photo) }}" alt="{{ $artist->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                @endif
                                <div>
                                    <h2 class="font-sans font-black text-lg text-gray-900 leading-tight">{{ $artist->user->name }}</h2>
                                    <p class="font-sans text-xs text-lavender-600 font-semibold">{{ $artist->studio->city }}</p>
                                </div>
                            </div>

                            @if (empty($missingIds))
                                <div class="mt-3">
                                    <span class="font-sans font-bold text-xs bg-lima-300 text-gray-900 rounded-full px-3 py-1">
                                        ✓ 100% match
                                    </span>
                                </div>
                            @else
                                <div class="mt-3">
                                    <p class="font-sans font-bold text-xs uppercase tracking-wide text-red-500 mb-1.5">Missing</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach (\App\Models\Feature::whereIn('id', $missingIds)->get() as $feature)
                                            <span class="font-sans text-xs bg-red-50 text-red-600 rounded-full px-2.5 py-1">{{ $feature->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($extraFeatures->isNotEmpty())
                                <div class="mt-3">
                                    <p class="font-sans font-bold text-xs uppercase tracking-wide text-lima-600 mb-1.5">Extras</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($extraFeatures as $feature)
                                            <span class="font-sans text-xs bg-lima-100 text-lima-700 rounded-full px-2.5 py-1">{{ $feature->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>