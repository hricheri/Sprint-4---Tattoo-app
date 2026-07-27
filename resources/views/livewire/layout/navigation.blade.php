<?php

use App\Livewire\Actions\Logout;
use App\Models\Like;
use Livewire\Volt\Component;

new class extends Component
{
    public $favoritesCount = 0;

    public function mount()
    {
        $artist = auth()->user()->artist ?? null;

        if ($artist) {
            $this->favoritesCount = Like::where('liker_artist_id', $artist->id)->count();
        }
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ open: false }">
    <!-- Slim top bar: logo + mobile hamburger only -->
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('explore') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Hamburger (mobile only) -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu (mobile) -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('explore')" :active="request()->routeIs('explore')" wire:navigate>
                    {{ __('Explore') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('artist-profile.show')" :active="request()->routeIs('artist-profile.*')" wire:navigate>
                    {{ __('My Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('availability')" :active="request()->routeIs('availability')" wire:navigate>
                    {{ __('My Availability') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('swaps.index')" :active="request()->routeIs('swaps.*')" wire:navigate>
                    {{ __('My Swaps') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('favorites')" :active="request()->routeIs('favorites')" wire:navigate>
                    {{ __('Favorites') }} @if ($favoritesCount > 0) ({{ $favoritesCount }}) @endif
                </x-responsive-nav-link>
            </div>

            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile')" wire:navigate>
                        {{ __('Account Settings') }}
                    </x-responsive-nav-link>

                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Floating vertical icon dock (desktop only) -->
    <div class="hidden sm:flex fixed right-5 top-1/2 -translate-y-1/2 z-50 flex-col items-center gap-1 bg-white rounded-full shadow-lg border border-gray-100 p-2">

        @if (request()->routeIs('artist-profile.*'))
            <a href="{{ route('explore') }}" wire:navigate
                class="p-3 rounded-full text-gray-500 hover:text-lavender-600 hover:bg-lavender-50 transition"
                title="Explore">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3a14.5 14.5 0 010 18M12 3a14.5 14.5 0 000 18" />
                </svg>
            </a>
        @else
            <a href="{{ route('artist-profile.show') }}" wire:navigate
                class="p-3 rounded-full text-gray-500 hover:text-lavender-600 hover:bg-lavender-50 transition"
                title="My Profile">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </a>
        @endif

        <a href="{{ route('availability') }}" wire:navigate
            class="p-3 rounded-full transition {{ request()->routeIs('availability') ? 'text-lavender-600 bg-lavender-50' : 'text-gray-500 hover:text-lavender-600 hover:bg-lavender-50' }}"
            title="My Availability">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
        </a>

        <a href="{{ route('swaps.index') }}" wire:navigate
            class="p-3 rounded-full transition {{ request()->routeIs('swaps.*') ? 'text-lavender-600 bg-lavender-50' : 'text-gray-500 hover:text-lavender-600 hover:bg-lavender-50' }}"
            title="My Swaps">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-9L21 3m0 0l-4.5 4.5M21 3H7.5" />
            </svg>
        </a>

        <a href="{{ route('favorites') }}" wire:navigate
            class="relative p-3 rounded-full transition {{ request()->routeIs('favorites') ? 'text-lavender-600 bg-lavender-50' : 'text-gray-500 hover:text-lavender-600 hover:bg-lavender-50' }}"
            title="Favorites">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
            @if ($favoritesCount > 0)
                <span class="absolute top-0.5 right-0.5 bg-lima-300 text-gray-900 text-[10px] font-sans font-bold rounded-full w-4 h-4 flex items-center justify-center">
                    {{ $favoritesCount }}
                </span>
            @endif
        </a>

        <div class="w-6 border-t border-gray-100 my-1"></div>

        <x-dropdown align="left" width="48">
            <x-slot name="trigger">
                <button class="p-3 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-4 py-2 text-xs text-gray-400 font-sans">
                    {{ auth()->user()->name }}
                </div>
                <x-dropdown-link :href="route('profile')" wire:navigate>
                    {{ __('Account Settings') }}
                </x-dropdown-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-dropdown-link>
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </button>
            </x-slot>
        </x-dropdown>
    </div>
</div>