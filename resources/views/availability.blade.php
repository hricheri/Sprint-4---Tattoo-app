<x-app-layout>
    <x-slot name="header">
        <h1 class="font-sans font-black text-3xl text-gray-900">My Availability</h1>
        <p class="font-sans text-gray-500 mt-1">Mark the dates you're free to travel. Confirmed swaps are locked automatically.</p>
    </x-slot>

    <div class="max-w-md mx-auto py-10 px-4">
        <livewire:availability-calendar />
    </div>
</x-app-layout>