<?php

namespace App\Services\Analytics;

use App\Models\User;
use App\Services\Spotify\SpotifyApiService;

class DecadeAnalysisService
{
    public function __construct(
        protected SpotifyApiService $spotifyApi
    ) {}

    /**
     * Analyze musical decades based on release dates of user's top tracks.
     *
     * @return array{
     *     decades: array<int, array{
     *         decade: string,
     *         label: string,
     *         count: int,
     *         percentage: float,
     *         representative_track: ?array{
     *             id: string,
     *             name: string,
     *             artist: string,
     *             album: string,
     *             release_date: string,
     *             image_url: ?string,
     *             spotify_url: ?string
     *         }
     *     }>,
     *     predominant_decade: ?string,
     *     total_tracks_analyzed: int
     * }
     */
    public function getDecadeAnalysis(User $user, string $timeRange = 'medium_term'): array
    {
        $topTracksResponse = $this->spotifyApi->getTopTracks($user, $timeRange, 50);
        $tracks = $topTracksResponse['items'] ?? [];

        if (empty($tracks)) {
            return [
                'decades' => [],
                'predominant_decade' => null,
                'total_tracks_analyzed' => 0,
            ];
        }

        $decadeGroups = [];
        $totalAnalyzed = 0;

        foreach ($tracks as $track) {
            $releaseDate = $track['album']['release_date'] ?? null;
            if (!$releaseDate) {
                continue;
            }

            // Extract 4-digit year from "YYYY", "YYYY-MM", or "YYYY-MM-DD"
            if (!preg_match('/^(\d{4})/', $releaseDate, $matches)) {
                continue;
            }

            $year = (int) $matches[1];
            if ($year < 1920 || $year > 2030) {
                continue;
            }

            $decadeStart = (int) (floor($year / 10) * 10);
            $decadeKey = "{$decadeStart}s";
            $decadeLabel = "Anos {$decadeStart}";

            if (!isset($decadeGroups[$decadeKey])) {
                $decadeGroups[$decadeKey] = [
                    'decade' => $decadeKey,
                    'label' => $decadeLabel,
                    'year_start' => $decadeStart,
                    'count' => 0,
                    'tracks' => [],
                ];
            }

            $decadeGroups[$decadeKey]['count']++;
            $decadeGroups[$decadeKey]['tracks'][] = $track;
            $totalAnalyzed++;
        }

        if ($totalAnalyzed === 0) {
            return [
                'decades' => [],
                'predominant_decade' => null,
                'total_tracks_analyzed' => 0,
            ];
        }

        // Sort decades chronologically
        uasort($decadeGroups, fn ($a, $b) => $a['year_start'] <=> $b['year_start']);

        $formattedDecades = [];
        $maxCount = -1;
        $predominantDecade = null;

        foreach ($decadeGroups as $group) {
            $percentage = round(($group['count'] / $totalAnalyzed) * 100, 1);

            // Find representative track (highest popularity within this decade)
            $bestTrack = null;
            $bestPopularity = -1;

            foreach ($group['tracks'] as $candidate) {
                $popularity = (int) ($candidate['popularity'] ?? 0);
                if ($popularity > $bestPopularity || $bestTrack === null) {
                    $bestPopularity = $popularity;
                    $bestTrack = $candidate;
                }
            }

            $representativeTrack = null;
            if ($bestTrack) {
                $artistNames = array_column($bestTrack['artists'] ?? [], 'name');
                $imageUrl = $bestTrack['album']['images'][0]['url'] ?? null;
                $spotifyUrl = $bestTrack['external_urls']['spotify'] ?? null;

                $representativeTrack = [
                    'id' => $bestTrack['id'] ?? '',
                    'name' => $bestTrack['name'] ?? 'Música',
                    'artist' => implode(', ', $artistNames) ?: 'Artista',
                    'album' => $bestTrack['album']['name'] ?? '',
                    'release_date' => $bestTrack['album']['release_date'] ?? '',
                    'image_url' => $imageUrl,
                    'spotify_url' => $spotifyUrl,
                ];
            }

            if ($group['count'] > $maxCount) {
                $maxCount = $group['count'];
                $predominantDecade = $group['label'];
            }

            $formattedDecades[] = [
                'decade' => $group['decade'],
                'label' => $group['label'],
                'count' => $group['count'],
                'percentage' => $percentage,
                'representative_track' => $representativeTrack,
            ];
        }

        return [
            'decades' => array_values($formattedDecades),
            'predominant_decade' => $predominantDecade,
            'total_tracks_analyzed' => $totalAnalyzed,
        ];
    }
}

