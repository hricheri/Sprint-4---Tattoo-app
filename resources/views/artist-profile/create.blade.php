<x-app-layout>
    <x-slot name="header">
        <h1 class="font-sans font-black text-3xl text-gray-900">Creá tu perfil</h1>
        <p class="font-sans text-gray-500 mt-1">Contale a otros artistas quién sos y qué ofrecés para el intercambio.</p>
    </x-slot>

    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('artist-profile.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- === DATOS DEL ARTISTA === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-6">Sobre vos</h2>

                <div class="space-y-5">
                    <div>
                        <label for="bio" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Bio</label>
                        <textarea id="bio" name="bio" rows="3"
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400"
                            placeholder="Contale a otros artistas quién sos...">{{ old('bio') }}</textarea>
                        @error('bio') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="social_media_handle" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Redes sociales</label>
                            <input type="text" id="social_media_handle" name="social_media_handle" value="{{ old('social_media_handle') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400"
                                placeholder="@tuusuario">
                            @error('social_media_handle') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Email de contacto</label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400"
                                placeholder="tu@email.com">
                            @error('contact_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- === TU ESTUDIO === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-6">Tu estudio</h2>

                <div class="space-y-5">
                    <div>
                        <label for="studio_photo" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Foto del estudio</label>
                        <input type="file" id="studio_photo" name="studio_photo" accept="image/*"
                            class="w-full rounded-xl border-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-lavender-100 file:text-lavender-700 file:font-semibold file:px-4 file:py-2">
                        @error('studio_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="studio_name" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Nombre del estudio *</label>
                            <input type="text" id="studio_name" name="studio_name" value="{{ old('studio_name') }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('studio_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="studio_city" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Ciudad *</label>
                            <input type="text" id="studio_city" name="studio_city" value="{{ old('studio_city') }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('studio_city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="studio_address" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Dirección</label>
                        <input type="text" id="studio_address" name="studio_address" value="{{ old('studio_address') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                        @error('studio_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="studio_cost_type" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Tipo de costo *</label>
                            <select id="studio_cost_type" name="studio_cost_type" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                                <option value="">Elegí una opción</option>
                                <option value="renta_fija" @selected(old('studio_cost_type') == 'renta_fija')>Renta fija</option>
                                <option value="porcentaje" @selected(old('studio_cost_type') == 'porcentaje')>Porcentaje por tatuaje</option>
                                <option value="dueño_sin_costo" @selected(old('studio_cost_type') == 'dueño_sin_costo')>Soy dueño/a, sin costo</option>
                            </select>
                            @error('studio_cost_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="studio_cost_amount" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Monto (si aplica)</label>
                            <input type="number" step="0.01" id="studio_cost_amount" name="studio_cost_amount" value="{{ old('studio_cost_amount') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('studio_cost_amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <span class="block font-sans font-semibold text-sm text-gray-700 mb-2">Tipo de estudio *</span>
                        <div class="flex gap-3">
                            <label class="flex-1 flex items-center justify-center gap-2 rounded-xl border-2 border-gray-200 has-[:checked]:border-lavender-400 has-[:checked]:bg-lavender-50 px-4 py-3 cursor-pointer transition">
                                <input type="radio" name="studio_type" value="individual" class="text-lavender-500 focus:ring-lavender-400" @checked(old('studio_type') == 'individual') required>
                                <span class="font-sans font-semibold text-sm">Individual</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 rounded-xl border-2 border-gray-200 has-[:checked]:border-lavender-400 has-[:checked]:bg-lavender-50 px-4 py-3 cursor-pointer transition">
                                <input type="radio" name="studio_type" value="compartido" class="text-lavender-500 focus:ring-lavender-400" @checked(old('studio_type') == 'compartido')>
                                <span class="font-sans font-semibold text-sm">Compartido</span>
                            </label>
                        </div>
                        @error('studio_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="studio_access_instructions" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Instrucciones de acceso</label>
                        <textarea id="studio_access_instructions" name="studio_access_instructions" rows="2"
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">{{ old('studio_access_instructions') }}</textarea>
                        @error('studio_access_instructions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- === LO QUE OFRECÉS (features del estudio) === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lima-600 mb-1">Lo que ofrecés</h2>
                <p class="font-sans text-sm text-gray-500 mb-6">Seleccioná las características que tiene tu estudio.</p>

                @foreach (['must_have' => 'Must haves', 'additional' => 'Adicionales'] as $categoryKey => $categoryLabel)
                    <div class="mb-5">
                        <h3 class="font-sans font-bold text-sm uppercase tracking-wide text-gray-400 mb-3">{{ $categoryLabel }}</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($features->where('category', $categoryKey) as $feature)
                                <label class="flex items-center gap-2 rounded-full border-2 border-gray-200 has-[:checked]:border-lima-400 has-[:checked]:bg-lima-100 px-4 py-2 cursor-pointer transition">
                                    <input type="checkbox" name="studio_features[]" value="{{ $feature->id }}"
                                        class="rounded text-lima-500 focus:ring-lima-400"
                                        @checked(collect(old('studio_features'))->contains($feature->id))>
                                    <span class="font-sans text-sm">{{ $feature->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- === LO QUE NECESITÁS (preferencias del artista) === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-1">Lo que necesitás</h2>
                <p class="font-sans text-sm text-gray-500 mb-6">¿Qué características son indispensables en el estudio donde vas a tatuar?</p>

                @foreach (['must_have' => 'Must haves', 'additional' => 'Adicionales'] as $categoryKey => $categoryLabel)
                    <div class="mb-5">
                        <h3 class="font-sans font-bold text-sm uppercase tracking-wide text-gray-400 mb-3">{{ $categoryLabel }}</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($features->where('category', $categoryKey) as $feature)
                                <label class="flex items-center gap-2 rounded-full border-2 border-gray-200 has-[:checked]:border-lavender-400 has-[:checked]:bg-lavender-100 px-4 py-2 cursor-pointer transition">
                                    <input type="checkbox" name="feature_preferences[]" value="{{ $feature->id }}"
                                        class="rounded text-lavender-500 focus:ring-lavender-400"
                                        @checked(collect(old('feature_preferences'))->contains($feature->id))>
                                    <span class="font-sans text-sm">{{ $feature->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- === TU HOGAR === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <h2 class="font-sans font-extrabold text-xl text-lavender-700 mb-6">Tu hogar</h2>

                <div class="space-y-5">
                    <div>
                        <label for="home_photo" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Foto del hogar</label>
                        <input type="file" id="home_photo" name="home_photo" accept="image/*"
                            class="w-full rounded-xl border-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-lavender-100 file:text-lavender-700 file:font-semibold file:px-4 file:py-2">
                        @error('home_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="home_roommates_count" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Compañeros de piso *</label>
                            <input type="number" min="0" id="home_roommates_count" name="home_roommates_count" value="{{ old('home_roommates_count', 0) }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            <p class="text-xs text-gray-400 mt-1">0 si vivís sola/o</p>
                            @error('home_roommates_count') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="home_distance_to_studio_minutes" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Distancia al estudio (min)</label>
                            <input type="number" min="0" id="home_distance_to_studio_minutes" name="home_distance_to_studio_minutes" value="{{ old('home_distance_to_studio_minutes') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('home_distance_to_studio_minutes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="home_transport_type" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Cómo se llega *</label>
                            <select id="home_transport_type" name="home_transport_type" required
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                                <option value="">Elegí una opción</option>
                                <option value="caminando" @selected(old('home_transport_type') == 'caminando')>Caminando</option>
                                <option value="transporte_publico" @selected(old('home_transport_type') == 'transporte_publico')>Transporte público</option>
                                <option value="auto" @selected(old('home_transport_type') == 'auto')>Auto</option>
                            </select>
                            @error('home_transport_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="home_transport_cost" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Costo de transporte</label>
                            <input type="number" step="0.01" id="home_transport_cost" name="home_transport_cost" value="{{ old('home_transport_cost') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">
                            @error('home_transport_cost') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="home_access_instructions" class="block font-sans font-semibold text-sm text-gray-700 mb-1">Instrucciones de acceso</label>
                        <textarea id="home_access_instructions" name="home_access_instructions" rows="2"
                            class="w-full rounded-xl border-gray-300 focus:border-lavender-400 focus:ring-lavender-400">{{ old('home_access_instructions') }}</textarea>
                        @error('home_access_instructions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full font-sans font-extrabold text-lg bg-lima-300 hover:bg-lima-400 text-gray-900 rounded-2xl py-4 shadow-sm transition">
                Guardar perfil
            </button>
        </form>
    </div>
</x-app-layout>