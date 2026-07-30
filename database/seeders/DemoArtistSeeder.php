<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Availability;
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
                'description' => 'A cozy test studio used for local development.',
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
                'description' => 'A quiet one-bedroom apartment, walking distance from the studio.',
                'distance_to_studio_minutes' => 10,
                'access_instructions' => 'Apartment 2B, buzzer code 1234.',
                'photo' => 'https://picsum.photos/seed/testhome/600/300',
            ]);

            $testArtist->featurePreferences()->sync(
                collect($mustHaveIds)->random(min(5, count($mustHaveIds)))->toArray()
            );

            $this->seedRandomAvailability($testArtist);
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
                'description' => 'A welcoming space for touring artists, fully equipped and ready to work.',
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
                'description' => 'A comfortable home base, close to public transport and local spots.',
                'distance_to_studio_minutes' => rand(5, 30),
                'access_instructions' => 'Departamento 3B, portero automático.',
                'photo' => "https://picsum.photos/seed/home{$i}/600/300",
            ]);

            $this->seedRandomAvailability($artist);
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

    /**
     * Marca entre 8 y 15 días de disponibilidad aleatoria para un artista,
     * distribuidos en los próximos 60 días, para que haya datos con los que
     * probar el matching de fechas (calendario, sugerencias tras cancelación, etc.).
     */
    private function seedRandomAvailability(Artist $artist): void
    {
        $daysAhead = range(1, 60);
        shuffle($daysAhead);
        $chosenDays = array_slice($daysAhead, 0, rand(8, 15));

        foreach ($chosenDays as $dayOffset) {
            Availability::create([
                'artist_id' => $artist->id,
                'date' => now()->addDays($dayOffset)->format('Y-m-d'),
            ]);
        }
    }
}