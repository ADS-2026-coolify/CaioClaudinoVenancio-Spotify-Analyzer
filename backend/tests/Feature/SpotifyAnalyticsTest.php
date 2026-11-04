<?php

namespace Tests\Feature;

use App\Models\SpotifyToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpotifyAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'spotify_id' => 'spotify_user_test',
            'name' => 'Tester User',
            'display_name' => 'Tester User',
            'email' => 'test@example.com',
            'country' => 'BR',
            'product' => 'premium',
        ]);

        SpotifyToken::create([
            'user_id' => $this->user->id,
            'access_token' => 'valid_bearer_token',
            'refresh_token' => 'valid_refresh_token',
            'expires_at' => Carbon::now()->addHour(),
            'scopes' => 'user-top-read user-read-recently-played',
        ]);
    }

    public function test_artists_analytics_endpoint_returns_data_from_spotify(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/me/top/artists*' => Http::response([
                'items' => [
                    [
                        'id' => 'art_001',
                        'name' => 'Tame Impala',
                        'genres' => ['neo-psychedelia', 'indie rock'],
                        'popularity' => 85,
                        'followers' => ['total' => 10000000],
                        'images' => [
                            ['url' => 'https://example.com/tame.jpg'],
                        ],
                        'external_urls' => ['spotify' => 'https://open.spotify.com/artist/art_001'],
                    ],
                ],
                'total' => 1,
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/analytics/artists?time_range=medium_term');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'time_range' => 'medium_term',
                'total' => 1,
                'average_popularity' => 85.0,
            ])
            ->assertJsonPath('artists.0.name', 'Tame Impala')
            ->assertJsonPath('artists.0.rank', 1);
    }

    public function test_tracks_analytics_endpoint_returns_data_with_decades(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/me/top/tracks*' => Http::response([
                'items' => [
                    [
                        'id' => 'trk_001',
                        'uri' => 'spotify:track:trk_001',
                        'name' => 'Let It Happen',
                        'artists' => [['name' => 'Tame Impala']],
                        'album' => [
                            'name' => 'Currents',
                            'release_date' => '2015-07-17',
                            'images' => [['url' => 'https://example.com/currents.jpg']],
                        ],
                        'popularity' => 80,
                        'duration_ms' => 467000,
                        'external_urls' => ['spotify' => 'https://open.spotify.com/track/trk_001'],
                    ],
                ],
                'total' => 1,
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/analytics/tracks?time_range=medium_term');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1,
            ])
            ->assertJsonPath('tracks.0.name', 'Let It Happen')
            ->assertJsonPath('tracks.0.decade', '2010s');
    }

    public function test_genres_analytics_calculates_proper_distribution_and_obscure_genre(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/me/top/artists*' => Http::response([
                'items' => [
                    [
                        'name' => 'Artist A',
                        'genres' => ['indie rock', 'alternative rock'],
                    ],
                    [
                        'name' => 'Artist B',
                        'genres' => ['indie rock', 'bossa nova vintage'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/analytics/genres?time_range=medium_term');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_unique_genres' => 3,
                    'total_genre_occurrences' => 4,
                ],
            ])
            ->assertJsonPath('data.top_genres.0.name', 'Indie Rock')
            ->assertJsonPath('data.top_genres.0.count', 2);
    }

    public function test_recently_played_endpoint_returns_tracks(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/me/player/recently-played*' => Http::response([
                'items' => [
                    [
                        'track' => [
                            'id' => 'trk_rec_1',
                            'name' => 'Recent Song',
                            'artists' => [['name' => 'Recent Artist']],
                            'album' => [
                                'name' => 'Recent Album',
                                'images' => [['url' => 'https://example.com/rec.jpg']],
                            ],
                            'duration_ms' => 180000,
                            'external_urls' => ['spotify' => 'https://open.spotify.com/track/trk_rec_1'],
                        ],
                        'played_at' => Carbon::now()->subMinutes(10)->toISOString(),
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/analytics/recently-played');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1,
            ])
            ->assertJsonPath('tracks.0.name', 'Recent Song');
    }

    public function test_playlist_generation_returns_candidate_tracks(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/me/top/artists*' => Http::response([
                'items' => [
                    [
                        'name' => 'Tame Impala',
                        'genres' => ['neo-psychedelia', 'ambient'],
                    ],
                ],
            ], 200),
            'https://api.spotify.com/v1/me/top/tracks*' => Http::response([
                'items' => [],
            ], 200),
            'https://api.spotify.com/v1/search*' => Http::response([
                'tracks' => [
                    'items' => [
                        [
                            'id' => 'search_t1',
                            'uri' => 'spotify:track:search1234567890',
                            'name' => 'Rainy Calm Song',
                            'artists' => [['name' => 'Calm Artist']],
                            'album' => [
                                'name' => 'Calm Album',
                                'release_date' => '2023-01-01',
                                'images' => [['url' => 'https://example.com/calm.jpg']],
                            ],
                            'duration_ms' => 200000,
                            'popularity' => 70,
                            'external_urls' => ['spotify' => 'https://open.spotify.com/track/search_t1'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/playlists/generate', [
                'mood' => 'rain',
                'track_count' => 15,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'mood' => 'rain',
                    'mood_label' => 'Dia de Chuva',
                ],
            ]);
    }
}

