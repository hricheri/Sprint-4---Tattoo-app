@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-sans font-black text-3xl text-gray-900">Explore</h1>
            
        <p class="font-sans text-gray-500 mt-1">Discover artists to swap studio and home with.</p>
    </x-slot>

    <div class="max-w-md mx-auto py-10 px-4">

        @if (session('status'))
            <div class="bg-lima-100 text-lima-800 font-sans font-semibold rounded-2xl px-6 py-4 mb-6">
                {{ session('status') }}
            </div>
        @endif

        @if (!$artist)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
                <p class="font-sans font-semibold text-gray-500">No more artists to explore right now.</p>
                <p class="font-sans text-sm text-gray-400 mt-1">Check back later, or take a look at your favorites.</p>
            </div>
        @else
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">

                <div class="relative h-44 bg-gray-100">
                    @if ($artist->studio->photo)
                        <img src="{{ photo_url($artist->studio->photo) }}" alt="Studio" class="w-full h-full object-cover">
                    @endif
                    <span class="absolute top-3 left-3 font-sans font-extrabold text-xs uppercase tracking-wide bg-lima-300 text-gray-900 rounded-full px-3 py-1">
                        Studio
                    </span>
                </div>

                <div class="relative h-44 bg-gray-100">
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
                            <img src="{{ photo_url($artist->profile_photo) }}" alt="{{ $artist->user->name }}" class="w-12 h-12 rounded-full object-cover">
                        @endif
                        <div>
                            <h2 class="font-sans font-black text-xl text-gray-900 leading-tight">{{ $artist->user->name }}</h2>
                            <p class="font-sans text-sm text-lavender-600 font-semibold">{{ $artist->studio->city }}</p>
                        </div>
                    </div>

                    @if ($artist->bio)
                        <p class="font-sans text-sm text-gray-600 mt-3">{{ $artist->bio }}</p>
                    @endif

                    @if ($missingFeatures->isEmpty())
                        <div class="mt-4">
                            <span class="font-sans font-bold text-sm bg-lima-300 text-gray-900 rounded-full px-4 py-1.5">
                                ✓ 100% match on your must-haves
                            </span>
                        </div>
                    @else
                        <div class="mt-4">
                            <p class="font-sans font-bold text-xs uppercase tracking-wide text-red-500 mb-1.5">Missing</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($missingFeatures as $feature)
                                    <span class="font-sans text-xs bg-red-50 text-red-600 rounded-full px-3 py-1">{{ $feature->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($additionalFeatures->isNotEmpty())
                        <div class="mt-3">
                            <p class="font-sans font-bold text-xs uppercase tracking-wide text-lima-600 mb-1.5">Extras</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($additionalFeatures as $feature)
                                    <span class="font-sans text-xs bg-lima-100 text-lima-700 rounded-full px-3 py-1">{{ $feature->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

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
                        🤍 Like
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>