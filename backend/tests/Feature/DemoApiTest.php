<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_dashboard_returns_realistic_mock_data(): void
    {
        $response = $this->getJson('/api/demo/dashboard');

        $response->assertStatus(200)
            ->assertJson([
                'is_demo' => true,
                'success' => true,
            ])
            ->assertJsonStructure([
                'is_demo',
                'success',
                'data' => [
                    'user' => ['id', 'name', 'display_name', 'avatar_url', 'country', 'product'],
                    'greeting',
                    'stats' => [
                        'total_artists_analyzed',
                        'total_tracks_analyzed',
                        'predominant_genre',
                        'predominant_decade',
                        'favorite_artist',
                        'top_track',
                    ],
                    'top_artists_preview',
                    'top_tracks_preview',
                    'top_genres_preview',
                    'decades_summary',
                    'insights_preview',
                ],
            ]);
    }

    public function test_demo_artists_returns_ranked_artists(): void
    {
        $response = $this->getJson('/api/demo/analytics/artists?time_range=medium_term');

        $response->assertStatus(200)
            ->assertJson([
                'is_demo' => true,
                'success' => true,
            ])
            ->assertJsonCount(10, 'artists')
            ->assertJsonPath('artists.0.rank', 1)
            ->assertJsonPath('artists.0.name', 'Arctic Monkeys');
    }

    public function test_demo_tracks_returns_categorized_decades(): void
    {
        $response = $this->getJson('/api/demo/analytics/tracks');

        $response->assertStatus(200)
            ->assertJson([
                'is_demo' => true,
                'success' => true,
            ])
            ->assertJsonCount(10, 'tracks')
            ->assertJsonStructure([
                'tracks' => [
                    '*' => ['rank', 'name', 'artists', 'album', 'release_date', 'decade', 'popularity'],
                ],
            ]);
    }

    public function test_demo_genres_returns_distribution_and_obscure_genre(): void
    {
        $response = $this->getJson('/api/demo/analytics/genres');

        $response->assertStatus(200)
            ->assertJson([
                'is_demo' => true,
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'genres',
                    'top_genres',
                    'rare_genres',
                    'obscure_genre' => ['name', 'count', 'artist_sample'],
                    'total_unique_genres',
                    'diversity_score',
                ],
            ]);
    }

    public function test_demo_decades_returns_chronological_distribution(): void
    {
        $response = $this->getJson('/api/demo/analytics/decades');

        $response->assertStatus(200)
            ->assertJson([
                'is_demo' => true,
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'decades' => [
                        '*' => ['decade', 'label', 'count', 'percentage', 'representative_track'],
                    ],
                    'predominant_decade',
                    'total_tracks_analyzed',
                ],
            ]);
    }

    public function test_demo_playlist_generation_returns_preview(): void
    {
        $response = $this->postJson('/api/demo/playlists/generate', [
            'mood' => 'relax',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'is_demo' => true,
                'success' => true,
                'data' => [
                    'mood' => 'relax',
                    'mood_label' => 'Relaxar',
                    'mood_emoji' => '😌',
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'tracks' => [
                        '*' => ['id', 'uri', 'name', 'artists', 'album', 'duration_formatted'],
                    ],
                ],
            ]);
    }
}
