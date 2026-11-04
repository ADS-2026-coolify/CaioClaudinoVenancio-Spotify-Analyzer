<?php

namespace Tests\Feature;

use App\Models\SpotifyToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_redirect_endpoint_returns_spotify_url_and_stores_state(): void
    {
        $response = $this->getJson('/api/spotify/auth');

        $response->assertStatus(200)
            ->assertJsonStructure(['auth_url', 'state']);

        $data = $response->json();
        $this->assertStringContainsString('accounts.spotify.com/authorize', $data['auth_url']);
        $this->assertNotEmpty($data['state']);
    }

    public function test_user_endpoint_returns_unauthenticated_for_guest(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'authenticated' => false,
                'user' => null,
            ]);
    }

    public function test_user_endpoint_returns_profile_when_authenticated(): void
    {
        $user = User::create([
            'spotify_id' => 'test_spotify_123',
            'name' => 'Caio Teste',
            'display_name' => 'Caio Teste',
            'email' => 'caio@example.com',
            'avatar_url' => 'https://example.com/avatar.jpg',
            'country' => 'BR',
            'product' => 'premium',
        ]);

        SpotifyToken::create([
            'user_id' => $user->id,
            'access_token' => 'sample_access_token',
            'refresh_token' => 'sample_refresh_token',
            'expires_at' => Carbon::now()->addHour(),
            'scopes' => 'user-read-private',
        ]);

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'authenticated' => true,
                'has_spotify' => true,
                'user' => [
                    'id' => $user->id,
                    'spotify_id' => 'test_spotify_123',
                    'name' => 'Caio Teste',
                    'email' => 'caio@example.com',
                ],
            ]);
    }

    public function test_callback_fails_with_invalid_state(): void
    {
        $response = $this->withSession(['spotify_oauth_state' => 'correct_state'])
            ->get('/api/spotify/callback?code=mock_code&state=wrong_state');

        $response->assertRedirect();
        $this->assertStringContainsString('error=invalid_state', $response->headers->get('Location'));
    }

    public function test_callback_succeeds_and_authenticates_user_with_valid_code_and_state(): void
    {
        $state = 'valid_oauth_state_12345';

        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'mocked_access_token',
                'refresh_token' => 'mocked_refresh_token',
                'expires_in' => 3600,
                'scope' => 'user-read-private user-top-read',
            ], 200),
            'https://api.spotify.com/v1/me' => Http::response([
                'id' => 'spotify_user_999',
                'display_name' => 'Maria Silva',
                'email' => 'maria@example.com',
                'country' => 'BR',
                'product' => 'premium',
                'images' => [
                    ['url' => 'https://example.com/maria.jpg'],
                ],
            ], 200),
        ]);

        $response = $this->withSession(['spotify_oauth_state' => $state])
            ->get("/api/spotify/callback?code=valid_spotify_auth_code&state={$state}");

        $response->assertRedirect();
        $this->assertStringContainsString('dashboard?auth=success', $response->headers->get('Location'));

        $this->assertDatabaseHas('users', [
            'spotify_id' => 'spotify_user_999',
            'display_name' => 'Maria Silva',
            'email' => 'maria@example.com',
        ]);

        $this->assertDatabaseHas('spotify_tokens', [
            'scopes' => 'user-read-private user-top-read',
        ]);
    }

    public function test_protected_routes_require_authentication_and_spotify_token(): void
    {
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);

        // User without spotify token
        $userWithoutToken = User::create([
            'spotify_id' => 'user_no_token',
            'name' => 'Sem Token',
        ]);

        $response = $this->actingAs($userWithoutToken)->getJson('/api/dashboard');
        $response->assertStatus(401)
            ->assertJson([
                'error' => 'spotify_disconnected',
            ]);
    }

    public function test_disconnect_removes_token_and_logs_out(): void
    {
        $user = User::create([
            'spotify_id' => 'user_to_disconnect',
            'name' => 'Usuário Disconnect',
        ]);

        SpotifyToken::create([
            'user_id' => $user->id,
            'access_token' => 'token_to_delete',
            'refresh_token' => 'refresh_to_delete',
            'expires_at' => Carbon::now()->addHour(),
        ]);

        $response = $this->actingAs($user)->deleteJson('/api/spotify/disconnect');

        $response->assertStatus(200)
            ->assertJson(['disconnected' => true]);

        $this->assertDatabaseMissing('spotify_tokens', [
            'user_id' => $user->id,
        ]);

        $this->assertGuest();
    }
}

