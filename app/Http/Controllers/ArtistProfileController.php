<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtistProfileController extends Controller
{
    public function show()
    {
        $artist = Auth::user()->artist()->with(['studio.features', 'home', 'featurePreferences'])->first();

        if (!$artist) {
            return redirect()->route('artist-profile.create');
        }

        return view('artist-profile.show', compact('artist'));
    }

    public function create()
    {
        if (Auth::user()->artist) {
            return redirect()->route('artist-profile.show');
        }

        $features = Feature::orderBy('category')->orderBy('name')->get();

        return view('artist-profile.create', compact('features'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'bio' => 'nullable|string|max:500',
        'social_media_handle' => 'nullable|string|max:255',
        'contact_email' => 'nullable|email|max:255',

        'studio_name' => 'required|string|max:255',
        'studio_city' => 'required|string|max:255',
        'studio_address' => 'nullable|string|max:255',
        'studio_cost_type' => 'required|in:renta_fija,porcentaje,dueño_sin_costo',
        'studio_cost_amount' => 'nullable|numeric|min:0',
        'studio_type' => 'required|in:individual,compartido',
        'studio_access_instructions' => 'nullable|string|max:500',
        'studio_features' => 'nullable|array',
        'studio_features.*' => 'exists:features,id',

        'home_roommates_count' => 'required|integer|min:0',
        'home_distance_to_studio_minutes' => 'nullable|integer|min:0',
        'home_transport_type' => 'required|in:caminando,transporte_publico,auto',
        'home_transport_cost' => 'nullable|numeric|min:0',
        'home_access_instructions' => 'nullable|string|max:500',

        'feature_preferences' => 'nullable|array',
        'feature_preferences.*' => 'exists:features,id',
    ]);

    $artist = Artist::create([
        'user_id' => Auth::id(),
        'bio' => $validated['bio'] ?? null,
        'social_media_handle' => $validated['social_media_handle'] ?? null,
        'contact_email' => $validated['contact_email'] ?? null,
    ]);

    $studio = $artist->studio()->create([
        'name' => $validated['studio_name'],
        'city' => $validated['studio_city'],
        'address' => $validated['studio_address'] ?? null,
        'cost_type' => $validated['studio_cost_type'],
        'cost_amount' => $validated['studio_cost_amount'] ?? null,
        'studio_type' => $validated['studio_type'],
        'access_instructions' => $validated['studio_access_instructions'] ?? null,
    ]);

    $studio->features()->sync($validated['studio_features'] ?? []);

    $artist->home()->create([
        'roommates_count' => $validated['home_roommates_count'],
        'distance_to_studio_minutes' => $validated['home_distance_to_studio_minutes'] ?? null,
        'transport_type' => $validated['home_transport_type'],
        'transport_cost' => $validated['home_transport_cost'] ?? null,
        'access_instructions' => $validated['home_access_instructions'] ?? null,
    ]);

    $artist->featurePreferences()->sync($validated['feature_preferences'] ?? []);

    return redirect()->route('artist-profile.show')->with('status', 'Perfil creado correctamente.');
}

    public function edit()
    {
        $artist = Auth::user()->artist()->with(['studio.features', 'home', 'featurePreferences'])->first();

        if (!$artist) {
            return redirect()->route('artist-profile.create');
        }

        $features = Feature::orderBy('category')->orderBy('name')->get();

        return view('artist-profile.edit', compact('artist', 'features'));
    }

    public function update(Request $request)
{
    $artist = Auth::user()->artist;

    if (!$artist) {
        return redirect()->route('artist-profile.create');
    }

    $validated = $request->validate([
        'bio' => 'nullable|string|max:500',
        'social_media_handle' => 'nullable|string|max:255',
        'contact_email' => 'nullable|email|max:255',

        'studio_name' => 'required|string|max:255',
        'studio_city' => 'required|string|max:255',
        'studio_address' => 'nullable|string|max:255',
        'studio_cost_type' => 'required|in:renta_fija,porcentaje,dueño_sin_costo',
        'studio_cost_amount' => 'nullable|numeric|min:0',
        'studio_type' => 'required|in:individual,compartido',
        'studio_access_instructions' => 'nullable|string|max:500',
        'studio_features' => 'nullable|array',
        'studio_features.*' => 'exists:features,id',

        'home_roommates_count' => 'required|integer|min:0',
        'home_distance_to_studio_minutes' => 'nullable|integer|min:0',
        'home_transport_type' => 'required|in:caminando,transporte_publico,auto',
        'home_transport_cost' => 'nullable|numeric|min:0',
        'home_access_instructions' => 'nullable|string|max:500',

        'feature_preferences' => 'nullable|array',
        'feature_preferences.*' => 'exists:features,id',
    ]);

    $artist->update([
        'bio' => $validated['bio'] ?? null,
        'social_media_handle' => $validated['social_media_handle'] ?? null,
        'contact_email' => $validated['contact_email'] ?? null,
    ]);

    $artist->studio->update([
        'name' => $validated['studio_name'],
        'city' => $validated['studio_city'],
        'address' => $validated['studio_address'] ?? null,
        'cost_type' => $validated['studio_cost_type'],
        'cost_amount' => $validated['studio_cost_amount'] ?? null,
        'studio_type' => $validated['studio_type'],
        'access_instructions' => $validated['studio_access_instructions'] ?? null,
    ]);

    $artist->studio->features()->sync($validated['studio_features'] ?? []);

    $artist->home->update([
        'roommates_count' => $validated['home_roommates_count'],
        'distance_to_studio_minutes' => $validated['home_distance_to_studio_minutes'] ?? null,
        'transport_type' => $validated['home_transport_type'],
        'transport_cost' => $validated['home_transport_cost'] ?? null,
        'access_instructions' => $validated['home_access_instructions'] ?? null,
    ]);

    $artist->featurePreferences()->sync($validated['feature_preferences'] ?? []);

    return redirect()->route('artist-profile.show')->with('status', 'Perfil actualizado correctamente.');
}

    public function destroy()
    {
        $artist = Auth::user()->artist;

        if ($artist) {
            $artist->delete();
        }

        return redirect()->route('dashboard')->with('status', 'Perfil eliminado correctamente.');
    }
}