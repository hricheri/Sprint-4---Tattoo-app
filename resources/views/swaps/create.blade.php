<x-app-layout>
    <x-slot name="header">
        <h1 class="font-sans font-black text-3xl text-gray-900">Propose a Swap</h1>
        <p class="font-sans text-gray-500 mt-1">Pick the dates you'd like to swap with {{ $artist->user->name }}.</p>
    </x-slot>

    <div class="max-w-md mx-auto py-10 px-4">
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

            <form method="POST" action="{{ route('swaps.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="artist_b_id" value="{{ $artist->id }}">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Start date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                        @error('start_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block font-sans font-semibold text-sm text-gray-700 mb-1">End date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                        @error('end_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="includes_money_exchange" value="1"
                        class="rounded text-lavender-500 focus:ring-lavender-400">
                    <span class="font-sans text-sm text-gray-700">This swap includes a money exchange</span>
                </label>

                <button type="submit"
                    class="w-full font-sans font-extrabold text-lg bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-2xl py-4 shadow-sm transition">
                    Send Proposal
                </button>
            </form>
        </div>
    </div>
</x-app-layout>