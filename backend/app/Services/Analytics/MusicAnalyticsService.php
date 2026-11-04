<?php

namespace App\Services\Analytics;

use App\Models\User;
use App\Services\Spotify\SpotifyApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class MusicAnalyticsService
{
    public function __construct(
        protected SpotifyApiService $spotifyApi,
        protected GenreAnalysisService $genreService,
        protected DecadeAnalysisService $decadeService,
        protected MusicInsightsService $insightsService
    ) {}

    /**
     * Build the consolidated Dashboard payload for the authenticated user.
     */
    public function getDashboardData(User $user): array
    {
        $cacheKey = "user_dashboard_summary_{$user->id}";

        return Cache::remember($cacheKey, 300, function () use ($user) {
            // 1. Fetch top artists and tracks (medium_term is default for overall feel)
            $topArtistsResponse = $this->spotifyApi->getTopArtists($user, 'medium_term', 50);
            $topTracksResponse = $this->spotifyApi->getTopTracks($user, 'medium_term', 50);
            $recentlyPlayedResponse = $this->spotifyApi->getRecentlyPlayed($user, 10);

            $artists = $topArtistsResponse['items'] ?? [];
            $tracks = $topTracksResponse['items'] ?? [];
            $recent = $recentlyPlayedResponse['items'] ?? [];

            // 2. Perform genre and decade calculations
            $genreAnalysis = $this->genreService->getGenreAnalysis($user, 'medium_term');
            $decadeAnalysis = $this->decadeService->getDecadeAnalysis($user, 'medium_term');

            // 3. Greeting based on current local hour (BRT UTC-3)
            $hour = Carbon::now()->subHours(3)->hour;
            $greeting = match (true) {
                $hour >= 5 && $hour < 12 => 'Bom dia',
                $hour >= 12 && $hour < 18 => 'Boa tarde',
                default => 'Boa noite',
            };

            // 4. Metrics indicators
            $favoriteArtist = !empty($artists) ? [
                'id' => $artists[0]['id'],
                'name' => $artists[0]['name'],
                'image_url' => $artists[0]['images'][0]['url'] ?? null,
                'popularity' => $artists[0]['popularity'] ?? 0,
                'genres' => array_slice($artists[0]['genres'] ?? [], 0, 3),
            ] : null;

            $topTrack = !empty($tracks) ? [
                'id' => $tracks[0]['id'],
                'name' => $tracks[0]['name'],
                'artist' => implode(', ', array_column($tracks[0]['artists'] ?? [], 'name')),
                'album' => $tracks[0]['album']['name'] ?? '',
                'image_url' => $tracks[0]['album']['images'][0]['url'] ?? null,
                'spotify_url' => $tracks[0]['external_urls']['spotify'] ?? null,
            ] : null;

            $predominantGenre = !empty($genreAnalysis['top_genres'])
                ? $genreAnalysis['top_genres'][0]['name']
                : 'Diversos';

            $predominantDecade = $decadeAnalysis['predominant_decade'] ?? 'Variada';

            // 5. Format top 5 artists preview
            $topArtistsPreview = array_map(function ($artist, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $artist['id'],
                    'name' => $artist['name'],
                    'genres' => array_slice($artist['genres'] ?? [], 0, 3),
                    'popularity' => $artist['popularity'] ?? 0,
                    'image_url' => $artist['images'][0]['url'] ?? null,
                    'spotify_url' => $artist['external_urls']['spotify'] ?? null,
                ];
            }, array_slice($artists, 0, 5), array_keys(array_slice($artists, 0, 5)));

            // 6. Format top 5 tracks preview
            $topTracksPreview = array_map(function ($track, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $track['id'],
                    'name' => $track['name'],
                    'artists' => implode(', ', array_column($track['artists'] ?? [], 'name')),
                    'album' => $track['album']['name'] ?? '',
                    'release_date' => $track['album']['release_date'] ?? '',
                    'image_url' => $track['album']['images'][0]['url'] ?? null,
                    'spotify_url' => $track['external_urls']['spotify'] ?? null,
                ];
            }, array_slice($tracks, 0, 5), array_keys(array_slice($tracks, 0, 5)));

            // 7. Format recently played preview
            $recentPreview = array_map(function ($item) {
                $track = $item['track'] ?? [];
                return [
                    'id' => $track['id'] ?? '',
                    'name' => $track['name'] ?? '',
                    'artists' => implode(', ', array_column($track['artists'] ?? [], 'name')),
                    'album' => $track['album']['name'] ?? '',
                    'image_url' => $track['album']['images'][0]['url'] ?? null,
                    'played_at' => $item['played_at'] ?? null,
                    'spotify_url' => $track['external_urls']['spotify'] ?? null,
                ];
            }, array_slice($recent, 0, 5));

            // 8. Generate insights
            $insights = $this->insightsService->getInsights($user);

            return [
                'user' => [
                    'id' => $user->id,
                    'spotify_id' => $user->spotify_id,
                    'name' => $user->display_name ?: $user->name,
                    'display_name' => $user->display_name ?: $user->name,
                    'avatar_url' => $user->avatar_url,
                    'country' => $user->country,
                    'product' => $user->product,
                ],
                'greeting' => $greeting,
                'stats' => [
                    'total_artists_analyzed' => count($artists),
                    'total_tracks_analyzed' => count($tracks),
                    'predominant_genre' => $predominantGenre,
                    'predominant_decade' => $predominantDecade,
                    'favorite_artist' => $favoriteArtist,
                    'top_track' => $topTrack,
                    'unique_genres_count' => $genreAnalysis['total_unique_genres'] ?? 0,
                    'diversity_score' => $genreAnalysis['diversity_score'] ?? 0,
                ],
                'top_artists_preview' => $topArtistsPreview,
                'top_tracks_preview' => $topTracksPreview,
                'top_genres_preview' => array_slice($genreAnalysis['top_genres'] ?? [], 0, 5),
                'decades_summary' => $decadeAnalysis['decades'] ?? [],
                'recently_played_preview' => $recentPreview,
                'insights_preview' => array_slice($insights, 0, 3),
            ];
        });
    }
}

