<div>
    @if(count($newMatches) > 0)
        <style>
            @keyframes matchPopIn {
                0% { opacity: 0; transform: scale(0.7); }
                60% { opacity: 1; transform: scale(1.05); }
                100% { opacity: 1; transform: scale(1); }
            }
            @keyframes matchGlow {
                0%, 100% { box-shadow: 0 25px 60px -12px rgba(190, 242, 100, 0.55), 0 0 0 3px #bef264; }
                50% { box-shadow: 0 25px 70px -8px rgba(139, 92, 246, 0.6), 0 0 0 3px #a78bfa; }
            }
            .match-popup-overlay {
                position: fixed;
                inset: 0;
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
                background: linear-gradient(135deg, rgba(139, 92, 246, 0.55), rgba(190, 242, 100, 0.45));
            }
            .match-popup-card {
                width: 100%;
                max-width: 32rem;
                background: white;
                border-radius: 1.5rem;
                padding: 2rem;
                animation: matchPopIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards,
                           matchGlow 2s ease-in-out infinite 0.45s;
            }
        </style>

        <div class="match-popup-overlay">
            <div class="match-popup-card">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-sans font-black text-2xl text-lavender-700">It's a match! 🎉</h2>
                    <button type="button" wire:click="dismissAll" class="text-gray-400 hover:text-gray-600 text-xl" aria-label="Close">✕</button>
                </div>

                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($newMatches as $match)
                        <div wire:key="match-{{ $match['like_id'] }}" class="rounded-2xl bg-lavender-50 p-4">
                            <div class="flex items-center justify-between">
                                <button type="button" wire:click="viewProfile({{ $match['like_id'] }}, {{ $match['artist_id'] }})" class="flex items-center gap-3 flex-1 text-left">
                                    @if ($match['photo'])
                                        <img src="{{ $match['photo'] }}" alt="{{ $match['name'] }}" class="h-14 w-14 rounded-full object-cover ring-2 ring-lima-300">
                                    @endif
                                    <div>
                                        <p class="font-sans font-bold text-gray-900">{{ $match['name'] }} wants to swap!</p>
                                        @if($match['city'])
                                            <p class="font-sans text-sm text-gray-500">{{ $match['city'] }} · tap to view profile</p>
                                        @endif
                                    </div>
                                </button>
                                <button type="button" wire:click.stop="dismiss({{ $match['like_id'] }})" class="font-sans text-sm font-bold text-gray-400 hover:text-gray-600 px-2 shrink-0">✕</button>
                            </div>
                            <button type="button" wire:click="proposeSwap({{ $match['like_id'] }}, {{ $match['artist_id'] }})"
                                class="mt-3 w-full font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full py-2.5 transition">
                                Set Dates
                            </button>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('swaps.index') }}" wire:click="dismissAll" class="mt-6 block w-full font-sans font-semibold text-sm text-gray-500 hover:text-gray-700 text-center">View all matches in Swaps</a>
            </div>
        </div>
    @endif
</div>