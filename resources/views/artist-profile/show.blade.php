@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-sans font-black text-3xl text-gray-900">Mi Perfil</h1>
            <div class="flex gap-2">
                <a href="{{ route('artist-profile.preview') }}"
                    class="font-sans font-extrabold text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full px-5 py-2 transition">
                    Vista previa
                </a>
                <a href="{{ route('artist-profile.edit') }}"
                    class="font-sans font-extrabold text-sm bg-lavender-300 hover:bg-lavender-400 text-gray-900 rounded-full px-5 py-2 transition">
                    Editar
                </a>
                <form method="POST" action="{{ route('artist-profile.destroy') }}" onsubmit="return confirm('¿Segura que querés eliminar tu perfil? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="font-sans font-extrabold text-sm bg-red-100 hover:bg-red-200 text-red-700 rounded-full px-5 py-2 transition">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">

        @if (session('status'))
            <div class="bg-lima-100 text-lima-800 font-sans font-semibold rounded-2xl px-6 py-4">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <div class="flex items-center gap-4 mb-4">
                @if ($artist->profile_photo)
                    <img src="{{ Storage::url($artist->profile_photo) }}" alt="Tu foto de perfil" class="w-16 h-16 rounded-full object-cover">
                @endif
                <h2 class="font-sans font-extrabold text-xl text-lavender-700">Sobre vos</h2>
            </div>
            <p class="font-sans text-gray-700 mb-4">{{ $artist->bio ?: 'Todavía no agregaste una bio.' }}</p>
            <div class="flex flex-wrap gap-4 text-sm font-sans text-gray-500">
                @if ($artist->social_media_handle)
                    <span>📷 {{ $artist->social_media_handle }}</span>
                @endif
                @if ($artist->contact_email)
                    <span>✉️ {{ $artist->contact_email }}</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($artist->studio->photo)
                <img src="{{ Storage::url($artist->studio->photo) }}" alt="Foto del estudio" class="w-full h-56 object-cover">
            @endif
            <div class="p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-1">{{ $artist->studio->name }}</h2>
                <p class="font-sans text-sm text-gray-500 mb-4">{{ $artist->studio->city }}@if($artist->studio->address), {{ $artist->studio->address }}@endif</p>

                <div class="grid sm:grid-cols-2 gap-3 mb-5 text-sm font-sans">
                    <div class="bg-gray-50 rounded-xl px-4 py-2">
                        <span class="text-gray-400">Costo:</span>
                        <span class="font-semibold text-gray-700">{{ str_replace('_', ' ', $artist->studio->cost_type) }}</span>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-2">
                        <span class="text-gray-400">Tipo:</span>
                        <span class="font-semibold text-gray-700">{{ ucfirst($artist->studio->studio_type) }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if ($offersAllMustHaves)
                        <span class="font-sans font-bold text-sm bg-lima-300 text-gray-900 rounded-full px-4 py-1.5">
                            ✓ Ofrece todos los must haves
                        </span>
                        @foreach ($artist->studio->features->where('category', 'additional') as $feature)
                            <span class="font-sans text-sm bg-lima-100 text-lima-700 rounded-full px-4 py-1.5">{{ $feature->name }}</span>
                        @endforeach
                    @else
                        @foreach ($artist->studio->features as $feature)
                            <span class="font-sans text-sm bg-lima-100 text-lima-700 rounded-full px-4 py-1.5">{{ $feature->name }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-4">Lo que necesitás</h2>
            <div class="flex flex-wrap gap-2">
                @if ($needsAllMustHaves)
                    <span class="font-sans font-bold text-sm bg-lavender-300 text-gray-900 rounded-full px-4 py-1.5">
                        ✓ Necesita todos los must haves
                    </span>
                    @foreach ($artist->featurePreferences->where('category', 'additional') as $feature)
                        <span class="font-sans text-sm bg-lavender-100 text-lavender-700 rounded-full px-4 py-1.5">{{ $feature->name }}</span>
                    @endforeach
                @else
                    @forelse ($artist->featurePreferences as $feature)
                        <span class="font-sans text-sm bg-lavender-100 text-lavender-700 rounded-full px-4 py-1.5">{{ $feature->name }}</span>
                    @empty
                        <p class="font-sans text-sm text-gray-400">No marcaste ninguna preferencia todavía.</p>
                    @endforelse
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($artist->home->photo)
                <img src="{{ Storage::url($artist->home->photo) }}" alt="Foto del hogar" class="w-full h-56 object-cover">
            @endif
            <div class="p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-4">Tu hogar</h2>

                <div class="grid sm:grid-cols-2 gap-3 text-sm font-sans">
                    <div class="bg-gray-50 rounded-xl px-4 py-2">
                        <span class="text-gray-400">Compañeros de piso:</span>
                        <span class="font-semibold text-gray-700">{{ $artist->home->roommates_count }}</span>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-2">
                        <span class="text-gray-400">Cómo se llega:</span>
                        <span class="font-semibold text-gray-700">{{ str_replace('_', ' ', $artist->home->transport_type) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="font-sans text-xs text-gray-400 text-center">
            Las instrucciones de acceso a tu estudio y hogar se comparten automáticamente una vez que se confirma un intercambio.
        </p>
    </div>
</x-app-layout>