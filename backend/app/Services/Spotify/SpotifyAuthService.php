<?php

namespace App\Services\Spotify;

use App\Exceptions\SpotifyApiException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyAuthService
{
    public function __construct(
        protected SpotifyTokenService $tokenService
    ) {}

    /**
     * Build the Spotify Authorization URL for the OAuth 2.0 Authorization Code flow.
     */
    public function getAuthorizationUrl(string $state): string
    {
        $authUrl = config('spotify.auth_url', 'https://accounts.spotify.com/authorize');
        $clientId = config('spotify.client_id');
        $redirectUri = config('spotify.redirect_uri');
        $scopes = implode(' ', config('spotify.scopes', []));

        $params = http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => $scopes,
            'show_dialog' => 'true',
        ]);

        return "{$authUrl}?{$params}";
    }

    /**
     * Exchange the authorization code returned by Spotify for access and refresh tokens.
     *
     * @throws SpotifyApiException
     */
    public function handleCallback(string $code): array
    {
        $clientId = config('spotify.client_id');
        $clientSecret = config('spotify.client_secret');
        $redirectUri = config('spotify.redirect_uri');
        $tokenUrl = config('spotify.token_url', 'https://accounts.spotify.com/api/token');

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->timeout(15)
            ->post($tokenUrl, [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

        if ($response->failed()) {
            Log::error('Erro ao trocar authorization code por token no Spotify', [
                'status_code' => $response->status(),
            ]);

            throw SpotifyApiException::fromResponse($response);
        }

        return $response->json();
    }

    /**
     * Fetch user profile from Spotify /me endpoint, create or update the User model,
     * and persist encrypted tokens via SpotifyTokenService.
     *
     * @throws SpotifyApiException
     */
    public function getOrCreateUser(array $tokenData): User
    {
        $apiBaseUrl = config('spotify.api_base_url', 'https://api.spotify.com/v1');

        $profileResponse = Http::withToken($tokenData['access_token'])
            ->timeout(15)
            ->get("{$apiBaseUrl}/me");

        if ($profileResponse->failed()) {
            Log::error('Falha ao obter perfil do usuário no Spotify', [
                'status_code' => $profileResponse->status(),
            ]);

            throw SpotifyApiException::fromResponse($profileResponse);
        }

        $spotifyProfile = $profileResponse->json();
        $spotifyId = $spotifyProfile['id'];

        // Determine best avatar URL
        $avatarUrl = null;
        if (!empty($spotifyProfile['images']) && is_array($spotifyProfile['images'])) {
            $avatarUrl = $spotifyProfile['images'][0]['url'] ?? null;
        }

        $displayName = $spotifyProfile['display_name'] ?? $spotifyId;

        $user = User::updateOrCreate(
            ['spotify_id' => $spotifyId],
            [
                'name' => $displayName,
                'display_name' => $displayName,
                'email' => $spotifyProfile['email'] ?? null,
                'avatar_url' => $avatarUrl,
                'country' => $spotifyProfile['country'] ?? null,
                'product' => $spotifyProfile['product'] ?? 'free',
                'last_login_at' => Carbon::now(),
            ]
        );

        // Store tokens securely
        $this->tokenService->storeTokens($user, $tokenData);

        return $user;
    }
}
