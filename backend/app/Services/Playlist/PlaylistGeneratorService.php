<?php

namespace App\Services\Playlist;

use App\Enums\MoodCategory;
use App\Models\GeneratedPlaylist;
use App\Models\User;
use App\Services\Spotify\SpotifyApiService;
use Illuminate\Support\Facades\Log;

class PlaylistGeneratorService
{
    public function __construct(
        protected SpotifyApiService $spotifyApi
    ) {}

    /**
     * Generate a contextual playlist based on the chosen mood, user's top artists,
     * genres, and Spotify search queries (without using deprecated /recommendations).
     *
     * @return array{
     *     mood: string,
     *     mood_label: string,
     *     mood_emoji: string,
     *     tracks: array<int, array{
     *         id: string,
     *         uri: string,
     *         name: string,
     *         artists: string,
     *         album: string,
     *         release_date: string,
     *         duration_ms: int,
     *         duration_formatted: string,
     *         image_url: ?string,
     *         spotify_url: ?string,
     *         is_favorite_artist: bool
     *     }>,
     *     total_tracks: int,
     *     estimated_duration_minutes: int
     * }
     */
    public function generatePlaylist(User $user, MoodCategory $mood, int $trackCount = 20): array
    {
        $trackCount = min(50, max(10, $trackCount));

        // 1. Fetch user's top artists and top tracks
        $topArtistsResponse = $this->spotifyApi->getTopArtists($user, 'medium_term', 30);
        $topTracksResponse = $this->spotifyApi->getTopTracks($user, 'medium_term', 30);

        $userArtists = $topArtistsResponse['items'] ?? [];
        $userTracks = $topTracksResponse['items'] ?? [];

        // Map of favorite artist names (lowercased)
        $favoriteArtistNames = [];
        foreach ($userArtists as $artist) {
            $favoriteArtistNames[strtolower($artist['name'])] = true;
        }

        // 2. Extract user's genres
        $userGenres = [];
        foreach ($userArtists as $artist) {
            foreach ($artist['genres'] ?? [] as $g) {
                $userGenres[] = strtolower($g);
            }
        }
        $userGenres = array_unique($userGenres);

        // 3. Find matching genres with mood's related genres
        $moodGenres = $mood->relatedGenres();
        $matchingGenres = array_values(array_intersect($userGenres, $moodGenres));

        // 4. Build search queries combining mood keywords and genres
        $searchQueries = [];
        $keywords = $mood->searchKeywords();

        // Query 1: Mood primary keyword
        if (!empty($keywords[0])) {
            $searchQueries[] = $keywords[0];
        }

        // Query 2: Genre-based query
        $selectedGenre = !empty($matchingGenres) ? $matchingGenres[0] : (!empty($moodGenres) ? $moodGenres[0] : null);
        if ($selectedGenre) {
            $searchQueries[] = "genre:\"{$selectedGenre}\"";
        }

        // Query 3: Combine top artist with mood keyword if applicable
        if (!empty($userArtists[0]['name']) && !empty($keywords[1])) {
            $searchQueries[] = "artist:\"{$userArtists[0]['name']}\"";
        }

        // Query 4: Secondary keyword
        if (!empty($keywords[1])) {
            $searchQueries[] = $keywords[1];
        }

        // 5. Query Spotify Search API and collect candidate tracks
        $candidateTracks = [];
        $seenUris = [];

        // Also check if any existing user top tracks naturally fit the mood genre or artist
        foreach ($userTracks as $t) {
            $trackArtistName = strtolower($t['artists'][0]['name'] ?? '');
            if (isset($favoriteArtistNames[$trackArtistName]) && count($candidateTracks) < ($trackCount / 3)) {
                $uri = $t['uri'] ?? '';
                if ($uri && !isset($seenUris[$uri])) {
                    $seenUris[$uri] = true;
                    $candidateTracks[] = [
                        'track' => $t,
                        'score' => 10, // Higher priority for known favorites
                        'is_favorite_artist' => true,
                    ];
                }
            }
        }

        // Run search queries
        foreach ($searchQueries as $query) {
            if (count($candidateTracks) >= $trackCount * 2) {
                break;
            }

            try {
                $searchResult = $this->spotifyApi->searchTracks($user, $query, 15);
                $items = $searchResult['tracks']['items'] ?? [];

                foreach ($items as $item) {
                    $uri = $item['uri'] ?? '';
                    if (!$uri || isset($seenUris[$uri])) {
                        continue;
                    }

                    $seenUris[$uri] = true;
                    $firstArtist = strtolower($item['artists'][0]['name'] ?? '');
                    $isFavorite = isset($favoriteArtistNames[$firstArtist]);

                    $score = 5 + ($isFavorite ? 4 : 0) + (int) (($item['popularity'] ?? 50) / 25);

                    $candidateTracks[] = [
                        'track' => $item,
                        'score' => $score,
                        'is_favorite_artist' => $isFavorite,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Erro ao buscar faixas para playlist (query: {$query}): " . $e->getMessage());
            }
        }

        // Sort by score descending
        usort($candidateTracks, fn ($a, $b) => $b['score'] <=> $a['score']);

        // Slice to desired count
        $selectedCandidates = array_slice($candidateTracks, 0, $trackCount);

        // Format return tracks
        $totalDurationMs = 0;
        $formattedTracks = [];

        foreach ($selectedCandidates as $item) {
            $t = $item['track'];
            $durationMs = (int) ($t['duration_ms'] ?? 0);
            $totalDurationMs += $durationMs;

            $minutes = floor($durationMs / 60000);
            $seconds = floor(($durationMs % 60000) / 1000);
            $formattedDuration = sprintf('%d:%02d', $minutes, $seconds);

            $artistNames = array_column($t['artists'] ?? [], 'name');

            $formattedTracks[] = [
                'id' => $t['id'] ?? '',
                'uri' => $t['uri'] ?? '',
                'name' => $t['name'] ?? '',
                'artists' => implode(', ', $artistNames) ?: 'Artista',
                'album' => $t['album']['name'] ?? '',
                'release_date' => $t['album']['release_date'] ?? '',
                'duration_ms' => $durationMs,
                'duration_formatted' => $formattedDuration,
                'image_url' => $t['album']['images'][0]['url'] ?? null,
                'spotify_url' => $t['external_urls']['spotify'] ?? null,
                'is_favorite_artist' => $item['is_favorite_artist'],
            ];
        }

        $durationMinutes = (int) round($totalDurationMs / 60000);

        return [
            'mood' => $mood->value,
            'mood_label' => $mood->label(),
            'mood_emoji' => $mood->emoji(),
            'tracks' => $formattedTracks,
            'total_tracks' => count($formattedTracks),
            'estimated_duration_minutes' => $durationMinutes,
        ];
    }

    /**
     * Create the generated playlist in the user's Spotify account and save in DB.
     */
    public function createSpotifyPlaylist(
        User $user,
        string $name,
        string $description,
        bool $isPublic,
        MoodCategory $mood,
        array $trackUris
    ): GeneratedPlaylist {
        // 1. Create empty playlist in Spotify
        $playlistResponse = $this->spotifyApi->createPlaylist(
            $user,
            $user->spotify_id,
            $name,
            $description,
            $isPublic
        );

        $spotifyPlaylistId = $playlistResponse['id'] ?? null;
        $spotifyUrl = $playlistResponse['external_urls']['spotify'] ?? null;

        if (!$spotifyPlaylistId) {
            throw new \RuntimeException('Falha ao obter ID da playlist criada no Spotify.');
        }

        // 2. Add tracks to the newly created playlist
        if (!empty($trackUris)) {
            $this->spotifyApi->addTracksToPlaylist($user, $spotifyPlaylistId, $trackUris);
        }

        // 3. Persist record in database
        return GeneratedPlaylist::create([
            'user_id' => $user->id,
            'spotify_playlist_id' => $spotifyPlaylistId,
            'name' => $name,
            'description' => $description,
            'mood' => $mood,
            'track_count' => count($trackUris),
            'is_public' => $isPublic,
            'spotify_url' => $spotifyUrl,
        ]);
    }
}

