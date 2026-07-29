@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-sans font-black text-3xl text-gray-900">Preview</h1>
            <a href="{{ route('artist-profile.show') }}"
                class="font-sans font-semibold text-sm text-gray-500 hover:text-gray-700">
                ← Back to my profile
            </a>
        </div>
        <p class="font-sans text-gray-500 mt-1">This is how other artists will see you.</p>
    </x-slot>

    <div class="max-w-sm mx-auto py-10 px-4">
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">

            {{-- Studio photo --}}
            <div class="relative h-48 bg-gray-100">
                @if ($artist->studio->photo)
                    <img src="{{ photo_url($artist->studio->photo) }}" alt="Studio" class="w-full h-full object-cover">
                @endif
                <span class="absolute top-3 left-3 font-sans font-extrabold text-xs uppercase tracking-wide bg-lima-300 text-gray-900 rounded-full px-3 py-1">
                    Studio
                </span>
            </div>

            {{-- Curved separator --}}
            <div class="relative h-6 -mt-6">
                <svg viewBox="0 0 100 20" preserveAspectRatio="none" class="w-full h-6">
                    <path d="M0,0 C50,20 50,20 100,0 L100,20 L0,20 Z" fill="white" />
                </svg>
            </div>

            {{-- Home photo --}}
            <div class="relative h-48 bg-gray-100 -mt-6">
                @if ($artist->home->photo)
                    <img src="{{ photo_url($artist->home->photo) }}" alt="Home" class="w-full h-full object-cover">
                @endif
                <span class="absolute top-3 left-3 font-sans font-extrabold text-xs uppercase tracking-wide bg-lavender-300 text-gray-900 rounded-full px-3 py-1">
                    Home
                </span>
            </div>

            {{-- Artist info --}}
            <div class="p-6">
                <div class="flex items-center gap-3">
                    @if ($artist->profile_photo)
                        <img src="{{ photo_url($artist->profile_photo) }}" alt="{{ Auth::user()->name }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow -mt-12">
                    @endif
                    <div>
                        <h2 class="font-sans font-black text-2xl text-gray-900">{{ Auth::user()->name }}</h2>
                        <p class="font-sans font-semibold text-lavender-600">{{ $artist->studio->city }}</p>
                    </div>
                </div>

                @if ($artist->bio)
                    <p class="font-sans text-sm text-gray-600 mt-3">{{ $artist->bio }}</p>
                @endif

                <div class="flex flex-wrap gap-1.5 mt-4">
                    @if ($offersAllMustHaves)
                        <span class="font-sans font-bold text-xs bg-lima-300 text-gray-900 rounded-full px-3 py-1">
                            ✓ All must-haves
                        </span>
                        @foreach ($artist->studio->features->where('category', 'additional') as $feature)
                            <span class="font-sans text-xs bg-lima-100 text-lima-700 rounded-full px-3 py-1">{{ $feature->name }}</span>
                        @endforeach
                    @else
                        @foreach ($artist->studio->features as $feature)
                            <span class="font-sans text-xs bg-lima-100 text-lima-700 rounded-full px-3 py-1">{{ $feature->name }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>