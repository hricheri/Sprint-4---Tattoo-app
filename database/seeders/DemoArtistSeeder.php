<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Feature;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoArtistSeeder extends Seeder
{
    public function run(): void
    {
        $cities = ['Berlín', 'Barcelona', 'Ciudad de México', 'Tokio', 'Buenos Aires', 'Lisboa'];
        $mustHaveIds = Feature::where('category', 'must_have')->pluck('id')->toArray();
        $additionalIds = Feature::where('category', 'additional')->pluck('id')->toArray();

        $testUser = User::where('email', 'test@example.com')->first();

        if ($testUser && !$testUser->artist) {
            $testArtist = Artist::create([
                'user_id' => $testUser->id,
                'bio' => 'Test artist profile, auto-generated for local testing.',
                'social_media_handle' => '@test_artist',
                'contact_email' => 'test@example.com',
                'profile_photo' => 'https://picsum.photos/seed/testartist/200/200',
            ]);

            $testStudio = $testArtist->studio()->create([
                'name' => 'Test Studio',
                'city' => 'Barcelona',
                'address' => 'Carrer de Prova 1',
                'cost_type' => 'dueño_sin_costo',
                'studio_type' => 'individual',
                'access_instructions' => 'Ring the bell, second floor.',
                'photo' => 'https://picsum.photos/seed/teststudio/600/300',
            ]);

            $testStudio->features()->sync(
                collect($mustHaveIds)->random(min(10, count($mustHaveIds)))->toArray()
            );

            $testArtist->home()->create([
                'roommates_count' => 0,
                'distance_to_studio_minutes' => 10,
                'transport_type' => 'caminando',
                'access_instructions' => 'Apartment 2B, buzzer code 1234.',
                'photo' => 'https://picsum.photos/seed/testhome/600/300',
            ]);

            $testArtist->featurePreferences()->sync(
                collect($mustHaveIds)->random(min(5, count($mustHaveIds)))->toArray()
            );
        }

        for ($i = 1; $i <= 6; $i++) {
            $user = User::factory()->create([
                'name' => "Artista Demo $i",
                'email' => "demo{$i}@example.com",
            ]);

            $artist = Artist::create([
                'user_id' => $user->id,
                'bio' => 'Tatuador/a especializado en estilos variados, viajando por el mundo.',
                'social_media_handle' => '@demo_artist_' . $i,
                'contact_email' => "demo{$i}@example.com",
                'profile_photo' => "https://picsum.photos/seed/artist{$i}/200/200",
            ]);

            $studio = $artist->studio()->create([
                'name' => "Estudio Demo $i",
                'city' => $cities[array_rand($cities)],
                'address' => 'Calle Falsa 123',
                'cost_type' => ['renta_fija', 'porcentaje', 'dueño_sin_costo'][array_rand([0, 1, 2])],
                'cost_amount' => rand(50, 300),
                'studio_type' => ['individual', 'compartido'][array_rand([0, 1])],
                'access_instructions' => 'Tocá el timbre al llegar.',
                'photo' => "https://picsum.photos/seed/studio{$i}/600/300",
            ]);

            $randomMustHaves = collect($mustHaveIds)->random(rand(8, count($mustHaveIds)))->toArray();
            $randomAdditionals = collect($additionalIds)->random(rand(2, 6))->toArray();
            $studio->features()->sync(array_merge($randomMustHaves, $randomAdditionals));

            $artist->home()->create([
                'roommates_count' => rand(0, 2),
                'distance_to_studio_minutes' => rand(5, 30),
                'transport_type' => ['caminando', 'transporte_publico', 'auto'][array_rand([0, 1, 2])],
                'access_instructions' => 'Departamento 3B, portero automático.',
                'photo' => "https://picsum.photos/seed/home{$i}/600/300",
            ]);
        }

        $testArtist = $testUser?->fresh()->artist;

        if ($testArtist) {
            $mutualMatchIds = [1, 2, 3];

            foreach ($mutualMatchIds as $i) {
                $demoArtist = Artist::whereHas('user', fn ($q) => $q->where('email', "demo{$i}@example.com"))->first();

                if ($demoArtist) {
                    Like::create([
                        'liker_artist_id' => $demoArtist->id,
                        'liked_artist_id' => $testArtist->id,
                    ]);
                }
            }
        }
    }
}