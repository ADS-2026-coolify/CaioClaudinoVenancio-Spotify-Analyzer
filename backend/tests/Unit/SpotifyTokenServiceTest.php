<?php

namespace Tests\Unit;

use App\Models\SpotifyToken;
use App\Models\User;
use App\Services\Spotify\SpotifyTokenService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpotifyTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SpotifyTokenService $tokenService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = app(SpotifyTokenService::class);

        $this->user = User::create([
            'spotify_id' => 'unit_user_test',
            'name' => 'Unit User',
        ]);
    }

    public function test_tokens_are_stored_and_transparently_encrypted(): void
    {
        $tokenData = [
            'access_token' => 'plain_access_secret_123',
            'refresh_token' => 'plain_refresh_secret_456',
            'expires_in' => 3600,
            'scope' => 'user-top-read',
        ];

        $stored = $this->tokenService->storeTokens($this->user, $tokenData);

        $this->assertEquals('plain_access_secret_123', $stored->access_token);
        $this->assertEquals('plain_refresh_secret_456', $stored->refresh_token);

        // Verify direct raw database content is encrypted (not plain text)
        $raw = \DB::table('spotify_tokens')->where('id', $stored->id)->first();
        $this->assertNotEquals('plain_access_secret_123', $raw->access_token);
        $this->assertNotEquals('plain_refresh_secret_456', $raw->refresh_token);
    }

    public function test_is_expired_and_expires_within_minutes(): void
    {
        $token = SpotifyToken::create([
            'user_id' => $this->user->id,
            'access_token' => 'token1',
            'refresh_token' => 'token2',
            'expires_at' => Carbon::now()->addMinutes(3),
        ]);

        $this->assertFalse($token->isExpired());
        $this->assertTrue($token->expiresWithinMinutes(5));
        $this->assertFalse($token->expiresWithinMinutes(2));
    }

    public function test_get_valid_token_triggers_automatic_refresh_when_near_expiry(): void
    {
        SpotifyToken::create([
            'user_id' => $this->user->id,
            'access_token' => 'old_expiring_token',
            'refresh_token' => 'valid_refresh_token',
            'expires_at' => Carbon::now()->addMinutes(2), // expires in 2 mins < 5 min buffer
        ]);

        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'brand_new_refreshed_access_token',
                'expires_in' => 3600,
            ], 200),
        ]);

        $this->user->refresh();
        $validToken = $this->tokenService->getValidToken($this->user);

        $this->assertEquals('brand_new_refreshed_access_token', $validToken);
    }
}

