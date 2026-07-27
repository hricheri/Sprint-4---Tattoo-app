<x-app-layout>
    <x-slot name="header">
        <h1 class="font-sans font-black text-3xl text-gray-900">Set Dates</h1>
        <p class="font-sans text-gray-500 mt-1">Pick the dates you'd like to swap with {{ $artist->user->name }}.</p>
    </x-slot>

    <div class="max-w-md mx-auto py-10 px-4">
        <livewire:propose-swap :artist="$artist" />
    </div>
</x-app-layout>