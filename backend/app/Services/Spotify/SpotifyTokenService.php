<?php

namespace App\Services\Spotify;

use App\Exceptions\SpotifyApiException;
use App\Models\SpotifyToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyTokenService
{
    /**
     * Store or update Spotify tokens for a given user.
     */
    public function storeTokens(User $user, array $tokenData): SpotifyToken
    {
        $expiresIn = (int) ($tokenData['expires_in'] ?? 3600);
        $expiresAt = Carbon::now()->addSeconds($expiresIn);

        $attributes = [
            'access_token' => $tokenData['access_token'],
            'expires_at' => $expiresAt,
            'scopes' => $tokenData['scope'] ?? null,
        ];

        // Refresh token might not be returned on refreshes, keep existing if present
        if (!empty($tokenData['refresh_token'])) {
            $attributes['refresh_token'] = $tokenData['refresh_token'];
        }

        return SpotifyToken::updateOrCreate(
            ['user_id' => $user->id],
            $attributes
        );
    }

    /**
     * Retrieve a valid access token for the user, automatically refreshing if expired
     * or expiring within buffer minutes.
     *
     * @throws SpotifyApiException
     */
    public function getValidToken(User $user): string
    {
        $token = $user->spotifyToken;

        if (!$token) {
            throw SpotifyApiException::unauthorized();
        }

        $bufferMinutes = (int) config('spotify.token_refresh_buffer', 5);

        if ($token->expiresWithinMinutes($bufferMinutes)) {
            return $this->refreshToken($user);
        }

        return $token->access_token;
    }

    /**
     * Refresh the access token using the user's stored refresh token.
     *
     * @throws SpotifyApiException
     */
    public function refreshToken(User $user): string
    {
        $token = $user->spotifyToken;

        if (!$token || empty($token->refresh_token)) {
            Log::warning('Tentativa de renovar token sem refresh_token disponível', [
                'user_id' => $user->id,
            ]);
            throw SpotifyApiException::unauthorized();
        }

        $clientId = config('spotify.client_id');
        $clientSecret = config('spotify.client_secret');
        $tokenUrl = config('spotify.token_url', 'https://accounts.spotify.com/api/token');

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->timeout(15)
            ->post($tokenUrl, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token,
            ]);

        if ($response->failed()) {
            Log::error('Falha ao renovar token do Spotify', [
                'user_id' => $user->id,
                'status_code' => $response->status(),
            ]);

            throw SpotifyApiException::fromResponse($response);
        }

        $data = $response->json();
        $this->storeTokens($user, $data);

        return $data['access_token'];
    }

    /**
     * Delete stored tokens when disconnecting.
     */
    public function revokeTokens(User $user): void
    {
        $user->spotifyToken()->delete();

        Log::info('Tokens do Spotify revogados e excluídos', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Check if user has an existing valid (or refreshable) token record.
     */
    public function hasValidToken(User $user): bool
    {
        $token = $user->spotifyToken;

        if (!$token) {
            return false;
        }

        // Token is usable if not expired, or if it can be refreshed
        return !$token->isExpired() || !empty($token->refresh_token);
    }
}
