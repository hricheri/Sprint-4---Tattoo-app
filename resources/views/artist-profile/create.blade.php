<x-app-layout>
    <x-slot name="header">
        <h1 class="font-sans font-black text-3xl text-gray-900">Create your profile</h1>
        <p class="font-sans text-gray-500 mt-1">Tell other artists who you are and what you offer for the swap.</p>
    </x-slot>

    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('artist-profile.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- === ARTIST DATA === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-6">About you</h2>

                <div class="space-y-5">
                    <div>
                        <label for="artist_photo" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Profile photo</label>
                        <input type="file" id="artist_photo" name="artist_photo" accept="image/*"
                            class="w-full rounded-xl border-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-lavender-100 file:text-lavender-700 file:font-semibold file:px-4 file:py-2">
                        @error('artist_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="bio" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Bio</label>
                        <textarea id="bio" name="bio" rows="3"
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400"
                            placeholder="Tell other artists who you are...">{{ old('bio') }}</textarea>
                        @error('bio') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="social_media_handle" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Social media</label>
                            <input type="text" id="social_media_handle" name="social_media_handle" value="{{ old('social_media_handle') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400"
                                placeholder="@yourusername">
                            @error('social_media_handle') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Contact email</label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400"
                                placeholder="you@email.com">
                            @error('contact_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- === YOUR STUDIO === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-6">Your studio</h2>

                <div class="space-y-5">
                    <div>
                        <label for="studio_photo" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Studio photo</label>
                        <input type="file" id="studio_photo" name="studio_photo" accept="image/*"
                            class="w-full rounded-xl border-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-lavender-100 file:text-lavender-700 file:font-semibold file:px-4 file:py-2">
                        @error('studio_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="studio_name" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Studio name *</label>
                            <input type="text" id="studio_name" name="studio_name" value="{{ old('studio_name') }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('studio_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="studio_city" class="block font-sans font-semibold text-sm text-gray-700 mb-1">City *</label>
                            <input type="text" id="studio_city" name="studio_city" value="{{ old('studio_city') }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('studio_city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="studio_address" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Address</label>
                        <input type="text" id="studio_address" name="studio_address" value="{{ old('studio_address') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                        @error('studio_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="studio_cost_type" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Cost type *</label>
                            <select id="studio_cost_type" name="studio_cost_type" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                                <option value="">Choose an option</option>
                                <option value="renta_fija" @selected(old('studio_cost_type') == 'renta_fija')>Fixed rent</option>
                                <option value="porcentaje" @selected(old('studio_cost_type') == 'porcentaje')>Percentage per tattoo</option>
                                <option value="dueño_sin_costo" @selected(old('studio_cost_type') == 'dueño_sin_costo')>I own it, no cost</option>
                            </select>
                            @error('studio_cost_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="studio_cost_amount" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Amount (if applicable)</label>
                            <input type="number" step="0.01" id="studio_cost_amount" name="studio_cost_amount" value="{{ old('studio_cost_amount') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('studio_cost_amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <span class="block font-sans font-semibold text-sm text-gray-700 mb-2">Studio type *</span>
                        <div class="flex gap-3">
                            <label class="flex-1 flex items-center justify-center gap-2 rounded-xl border-2 border-gray-200 has-[:checked]:border-lavender-400 has-[:checked]:bg-lavender-50 px-4 py-3 cursor-pointer transition">
                                <input type="radio" name="studio_type" value="individual" class="text-lavender-500 focus:ring-lavender-400" @checked(old('studio_type') == 'individual') required>
                                <span class="font-sans font-semibold text-sm">Individual</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 rounded-xl border-2 border-gray-200 has-[:checked]:border-lavender-400 has-[:checked]:bg-lavender-50 px-4 py-3 cursor-pointer transition">
                                <input type="radio" name="studio_type" value="compartido" class="text-lavender-500 focus:ring-lavender-400" @checked(old('studio_type') == 'compartido')>
                                <span class="font-sans font-semibold text-sm">Shared</span>
                            </label>
                        </div>
                        @error('studio_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="studio_access_instructions" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Access instructions</label>
                        <textarea id="studio_access_instructions" name="studio_access_instructions" rows="2"
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">{{ old('studio_access_instructions') }}</textarea>
                        @error('studio_access_instructions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- === FEATURES (Alpine.js for quick-select buttons) === --}}
            <div x-data="{
                    studioFeatures: {{ Illuminate\Support\Js::from(old('studio_features', [])) }},
                    preferences: {{ Illuminate\Support\Js::from(old('feature_preferences', [])) }},
                    mustHaveIds: {{ Illuminate\Support\Js::from($features->where('category', 'must_have')->pluck('id')->values()) }},
                 }" class="space-y-8">

                {{-- === WHAT YOU OFFER === --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-4 mb-1">
                        <div>
                            <h2 class="font-sans font-extrabold text-xl text-lima-600">What you offer</h2>
                            <p class="font-sans text-sm text-gray-500">Select the features your studio has.</p>
                        </div>
                        <button type="button"
                            @click="studioFeatures = [...new Set([...studioFeatures, ...mustHaveIds])]"
                            class="shrink-0 font-sans font-semibold text-sm text-lima-700 bg-lima-100 hover:bg-lima-200 rounded-full px-4 py-2 transition">
                            Mark all must-haves
                        </button>
                    </div>

                    @foreach (['must_have' => 'Must haves', 'additional' => 'Additional'] as $categoryKey => $categoryLabel)
                        <div class="mt-5">
                            <h3 class="font-sans font-bold text-sm uppercase tracking-wide text-gray-400 mb-3">{{ $categoryLabel }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($features->where('category', $categoryKey) as $feature)
                                    <label class="flex items-center gap-2 rounded-full border-2 border-gray-200 has-[:checked]:border-lima-400 has-[:checked]:bg-lima-100 px-4 py-2 cursor-pointer transition">
                                        <input type="checkbox" name="studio_features[]" value="{{ $feature->id }}"
                                            x-model.number="studioFeatures"
                                            class="rounded text-lima-500 focus:ring-lima-400">
                                        <span class="font-sans text-sm whitespace-nowrap">{{ $feature->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- === WHAT YOU NEED === --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-4 mb-1">
                        <div>
                            <h2 class="font-sans font-extrabold text-xl text-lavender-700">What you need</h2>
                            <p class="font-sans text-sm text-gray-500">What features are non-negotiable in a studio you'll tattoo at?</p>
                        </div>
                        <button type="button"
                            @click="preferences = [...new Set([...preferences, ...studioFeatures])]"
                            class="shrink-0 font-sans font-semibold text-sm text-lavender-700 bg-lavender-100 hover:bg-lavender-200 rounded-full px-4 py-2 transition">
                            Copy from my studio
                        </button>
                    </div>

                    @foreach (['must_have' => 'Must haves', 'additional' => 'Additional'] as $categoryKey => $categoryLabel)
                        <div class="mt-5">
                            <h3 class="font-sans font-bold text-sm uppercase tracking-wide text-gray-400 mb-3">{{ $categoryLabel }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($features->where('category', $categoryKey) as $feature)
                                    <label class="flex items-center gap-2 rounded-full border-2 border-gray-200 has-[:checked]:border-lavender-400 has-[:checked]:bg-lavender-100 px-4 py-2 cursor-pointer transition">
                                        <input type="checkbox" name="feature_preferences[]" value="{{ $feature->id }}"
                                            x-model.number="preferences"
                                            class="rounded text-lavender-500 focus:ring-lavender-400">
                                        <span class="font-sans text-sm whitespace-nowrap">{{ $feature->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- === YOUR HOME === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-6">Your home</h2>

                <div class="space-y-5">
                    <div>
                        <label for="home_photo" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Home photo</label>
                        <input type="file" id="home_photo" name="home_photo" accept="image/*"
                            class="w-full rounded-xl border-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-lavender-100 file:text-lavender-700 file:font-semibold file:px-4 file:py-2">
                        @error('home_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="home_roommates_count" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Roommates *</label>
                            <input type="number" min="0" id="home_roommates_count" name="home_roommates_count" value="{{ old('home_roommates_count', 0) }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            <p class="text-xs text-gray-400 mt-1">0 if you live alone</p>
                            @error('home_roommates_count') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="home_distance_to_studio_minutes" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Distance to studio (min)</label>
                            <input type="number" min="0" id="home_distance_to_studio_minutes" name="home_distance_to_studio_minutes" value="{{ old('home_distance_to_studio_minutes') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('home_distance_to_studio_minutes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="home_transport_type" class="block font-sans font-semibold text-sm text-gray-700 mb-1">How to get there *</label>
                            <select id="home_transport_type" name="home_transport_type" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                                <option value="">Choose an option</option>
                                <option value="caminando" @selected(old('home_transport_type') == 'caminando')>Walking</option>
                                <option value="transporte_publico" @selected(old('home_transport_type') == 'transporte_publico')>Public transport</option>
                                <option value="auto" @selected(old('home_transport_type') == 'auto')>Car</option>
                            </select>
                            @error('home_transport_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="home_transport_cost" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Transport cost</label>
                            <input type="number" step="0.01" id="home_transport_cost" name="home_transport_cost" value="{{ old('home_transport_cost') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('home_transport_cost') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="home_access_instructions" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Access instructions</label>
                        <textarea id="home_access_instructions" name="home_access_instructions" rows="2"
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">{{ old('home_access_instructions') }}</textarea>
                        @error('home_access_instructions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full font-sans font-extrabold text-lg bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-2xl py-4 shadow-sm transition">
                Save profile
            </button>
        </form>
    </div>
</x-app-layout>