<?php

namespace App\Services\Spotify;

use App\Exceptions\SpotifyApiException;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyApiService
{
    public function __construct(
        protected SpotifyTokenService $tokenService
    ) {}

    /**
     * Perform an authenticated API request to Spotify.
     * Automatically handles 401 token refresh (retry once) and 429 rate limits.
     *
     * @throws SpotifyApiException
     */
    protected function request(User $user, string $method, string $endpoint, array $options = [], bool $isRetry = false): array
    {
        $token = $this->tokenService->getValidToken($user);
        $baseUrl = config('spotify.api_base_url', 'https://api.spotify.com/v1');
        $url = str_starts_with($endpoint, 'http') ? $endpoint : "{$baseUrl}/{$endpoint}";

        $request = Http::withToken($token)
            ->acceptJson()
            ->timeout(20);

        $response = match (strtoupper($method)) {
            'GET' => $request->get($url, $options['query'] ?? []),
            'POST' => $request->post($url, $options['json'] ?? $options['body'] ?? []),
            'PUT' => $request->put($url, $options['json'] ?? []),
            'DELETE' => $request->delete($url, $options['json'] ?? []),
            default => throw new \InvalidArgumentException("Método HTTP não suportado: {$method}"),
        };

        // Handle 401 Unauthorized - attempt token refresh and retry once
        if ($response->status() === 401 && !$isRetry) {
            Log::info('Spotify API retornou 401. Renovando token e tentando novamente...', [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
            ]);

            try {
                $this->tokenService->refreshToken($user);
                return $this->request($user, $method, $endpoint, $options, true);
            } catch (\Exception $e) {
                Log::error('Falha ao renovar token após 401 do Spotify: ' . $e->getMessage());
                throw SpotifyApiException::unauthorized();
            }
        }

        // Handle 429 Rate Limiting
        if ($response->status() === 429) {
            $retryAfter = (int) ($response->header('Retry-After') ?: 5);
            Log::warning("Spotify API 429 Rate Limit atingido. Retry-After: {$retryAfter}s", [
                'endpoint' => $endpoint,
                'user_id' => $user->id,
            ]);

            throw SpotifyApiException::rateLimited($retryAfter);
        }

        if ($response->failed()) {
            Log::error('Erro na resposta da Spotify API', [
                'status' => $response->status(),
                'endpoint' => $endpoint,
                'user_id' => $user->id,
            ]);

            throw SpotifyApiException::fromResponse($response);
        }

        return $response->json() ?? [];
    }

    /**
     * Get current authenticated user profile.
     */
    public function getCurrentUser(User $user): array
    {
        return $this->request($user, 'GET', 'me');
    }

    /**
     * Get user's top artists over a time range.
     * Caches result for 15 minutes.
     */
    public function getTopArtists(User $user, string $timeRange = 'medium_term', int $limit = 50): array
    {
        $cacheKey = "spotify_top_artists_{$user->id}_{$timeRange}_{$limit}";
        $ttl = config('spotify.cache_ttl.top_artists', 900);

        return Cache::remember($cacheKey, $ttl, function () use ($user, $timeRange, $limit) {
            return $this->request($user, 'GET', 'me/top/artists', [
                'query' => [
                    'time_range' => $timeRange,
                    'limit' => min(50, max(1, $limit)),
                ],
            ]);
        });
    }

    /**
     * Get user's top tracks over a time range.
     * Caches result for 15 minutes.
     */
    public function getTopTracks(User $user, string $timeRange = 'medium_term', int $limit = 50): array
    {
        $cacheKey = "spotify_top_tracks_{$user->id}_{$timeRange}_{$limit}";
        $ttl = config('spotify.cache_ttl.top_tracks', 900);

        return Cache::remember($cacheKey, $ttl, function () use ($user, $timeRange, $limit) {
            return $this->request($user, 'GET', 'me/top/tracks', [
                'query' => [
                    'time_range' => $timeRange,
                    'limit' => min(50, max(1, $limit)),
                ],
            ]);
        });
    }

    /**
     * Get user's recently played tracks.
     * Caches result for 5 minutes.
     */
    public function getRecentlyPlayed(User $user, int $limit = 50): array
    {
        $cacheKey = "spotify_recently_played_{$user->id}_{$limit}";
        $ttl = config('spotify.cache_ttl.recently_played', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($user, $limit) {
            return $this->request($user, 'GET', 'me/player/recently-played', [
                'query' => [
                    'limit' => min(50, max(1, $limit)),
                ],
            ]);
        });
    }

    /**
     * Search tracks on Spotify.
     */
    public function searchTracks(User $user, string $query, int $limit = 20): array
    {
        $cacheKey = 'spotify_search_' . md5($query) . "_{$limit}";
        $ttl = config('spotify.cache_ttl.search', 1800);

        return Cache::remember($cacheKey, $ttl, function () use ($user, $query, $limit) {
            return $this->request($user, 'GET', 'search', [
                'query' => [
                    'q' => $query,
                    'type' => 'track',
                    'limit' => min(50, max(1, $limit)),
                ],
            ]);
        });
    }

    /**
     * Get a single artist's catalog details.
     */
    public function getArtist(User $user, string $artistId): array
    {
        $cacheKey = "spotify_artist_{$artistId}";
        $ttl = config('spotify.cache_ttl.artist_details', 86400);

        return Cache::remember($cacheKey, $ttl, function () use ($user, $artistId) {
            return $this->request($user, 'GET', "artists/{$artistId}");
        });
    }

    /**
     * Get multiple artists (up to 50).
     */
    public function getArtists(User $user, array $artistIds): array
    {
        if (empty($artistIds)) {
            return ['artists' => []];
        }

        $slice = array_slice($artistIds, 0, 50);
        $idsParam = implode(',', $slice);

        return $this->request($user, 'GET', 'artists', [
            'query' => ['ids' => $idsParam],
        ]);
    }

    /**
     * Create a new playlist in the user's Spotify account.
     */
    public function createPlaylist(User $user, string $spotifyUserId, string $name, string $description = '', bool $public = true): array
    {
        return $this->request($user, 'POST', "users/{$spotifyUserId}/playlists", [
            'json' => [
                'name' => $name,
                'description' => $description,
                'public' => $public,
            ],
        ]);
    }

    /**
     * Add tracks to an existing playlist (in batches of 100).
     */
    public function addTracksToPlaylist(User $user, string $playlistId, array $trackUris): array
    {
        $chunks = array_chunk($trackUris, 100);
        $lastResponse = [];

        foreach ($chunks as $chunk) {
            $lastResponse = $this->request($user, 'POST', "playlists/{$playlistId}/tracks", [
                'json' => [
                    'uris' => array_values($chunk),
                ],
            ]);
        }

        return $lastResponse;
    }
}
