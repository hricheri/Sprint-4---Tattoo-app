<div>
    @if(count($confirmedSwaps) > 0)
        <style>
            @keyframes swapConfirmedPopIn {
                0% { opacity: 0; transform: scale(0.7); }
                60% { opacity: 1; transform: scale(1.05); }
                100% { opacity: 1; transform: scale(1); }
            }
        </style>

        <div class="fixed inset-0 z-[9998] flex items-center justify-center p-4" style="background: linear-gradient(135deg, rgba(169, 155, 196, 0.5), rgba(190, 242, 100, 0.4));">
            <div class="relative w-full max-w-lg bg-white rounded-3xl p-8 shadow-2xl" style="animation: swapConfirmedPopIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;">
                <button type="button" wire:click="dismissAll" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 text-xl" aria-label="Close">✕</button>

                <h2 class="font-sans font-black text-2xl text-lavender-700 mb-6 text-center">✈️ Swap confirmed!</h2>

                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($confirmedSwaps as $swap)
                        <div wire:key="confirmed-{{ $swap['swap_id'] }}" class="rounded-2xl bg-lima-50 border-2 border-lima-300 p-4">
                            <div class="flex items-center gap-3 mb-3">
                                @if ($swap['photo'])
                                    <img src="{{ $swap['photo'] }}" alt="{{ $swap['name'] }}" class="h-12 w-12 rounded-full object-cover">
                                @endif
                                <div>
                                    <p class="font-sans font-black text-gray-900">You're going to {{ $swap['city'] }}!</p>
                                    <p class="font-sans text-sm text-gray-600">{{ $swap['start'] }} – {{ $swap['end'] }} with {{ $swap['name'] }}</p>
                                </div>
                            </div>
                            <p class="font-sans text-xs text-gray-500 mb-3">
                                Send your guest announcement so {{ $swap['name'] }} can share it with their followers — and get access details unlocked once both sides post.
                            </p>
                            <div class="flex gap-2">
                                <button type="button" wire:click="goToSwaps" class="flex-1 font-sans font-extrabold text-sm bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-full py-2.5 transition">
                                    Send guest announcement →
                                </button>
                                <button type="button" wire:click="dismiss({{ $swap['swap_id'] }})" class="font-sans font-semibold text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full px-4 py-2.5 transition whitespace-nowrap">
                                    Remind me later
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>