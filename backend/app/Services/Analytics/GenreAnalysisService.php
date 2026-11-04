<?php

namespace App\Services\Analytics;

use App\Models\User;
use App\Services\Spotify\SpotifyApiService;

class GenreAnalysisService
{
    public function __construct(
        protected SpotifyApiService $spotifyApi
    ) {}

    /**
     * Compute comprehensive genre analysis based on the user's top artists.
     * Note: Spotify associates genres with artists, not individual tracks.
     *
     * @return array{
     *     genres: array<int, array{name: string, count: int, percentage: float}>,
     *     top_genres: array<int, array{name: string, count: int, percentage: float}>,
     *     rare_genres: array<int, array{name: string, count: int, percentage: float}>,
     *     obscure_genre: ?array{name: string, count: int, percentage: float, artist_sample: string},
     *     total_unique_genres: int,
     *     total_genre_occurrences: int,
     *     diversity_score: float
     * }
     */
    public function getGenreAnalysis(User $user, string $timeRange = 'medium_term'): array
    {
        $topArtistsResponse = $this->spotifyApi->getTopArtists($user, $timeRange, 50);
        $artists = $topArtistsResponse['items'] ?? [];

        if (empty($artists)) {
            return [
                'genres' => [],
                'top_genres' => [],
                'rare_genres' => [],
                'obscure_genre' => null,
                'total_unique_genres' => 0,
                'total_genre_occurrences' => 0,
                'diversity_score' => 0.0,
            ];
        }

        $genreCounts = [];
        $genreArtists = [];
        $totalOccurrences = 0;

        foreach ($artists as $artist) {
            $artistName = $artist['name'] ?? 'Artista Desconhecido';
            $genres = $artist['genres'] ?? [];

            foreach ($genres as $genre) {
                $genre = trim(strtolower($genre));
                if (empty($genre)) {
                    continue;
                }

                $genreCounts[$genre] = ($genreCounts[$genre] ?? 0) + 1;
                $genreArtists[$genre][] = $artistName;
                $totalOccurrences++;
            }
        }

        if ($totalOccurrences === 0) {
            return [
                'genres' => [],
                'top_genres' => [],
                'rare_genres' => [],
                'obscure_genre' => null,
                'total_unique_genres' => 0,
                'total_genre_occurrences' => 0,
                'diversity_score' => 0.0,
            ];
        }

        // Sort descending by count
        arsort($genreCounts);

        $formattedGenres = [];
        foreach ($genreCounts as $name => $count) {
            $formattedGenres[] = [
                'name' => ucwords($name),
                'count' => $count,
                'percentage' => round(($count / $totalOccurrences) * 100, 1),
            ];
        }

        $topGenres = array_slice($formattedGenres, 0, 10);

        // Rare genres are the ones with lowest count in the user's top artists
        $reversedGenres = array_reverse($formattedGenres);
        $rareGenres = array_slice($reversedGenres, 0, 5);

        // Obscure genre heuristic: least frequent genre, taking into account artist name
        $obscureGenre = null;
        if (!empty($rareGenres)) {
            $rarest = $rareGenres[0];
            $genreKey = strtolower($rarest['name']);
            $sampleArtist = $genreArtists[$genreKey][0] ?? 'Artista';

            $obscureGenre = [
                'name' => $rarest['name'],
                'count' => $rarest['count'],
                'percentage' => $rarest['percentage'],
                'artist_sample' => $sampleArtist,
            ];
        }

        $uniqueGenresCount = count($genreCounts);
        // Diversity score between 0.0 and 1.0
        $diversityScore = $totalOccurrences > 0
            ? round($uniqueGenresCount / $totalOccurrences, 2)
            : 0.0;

        return [
            'genres' => $formattedGenres,
            'top_genres' => $topGenres,
            'rare_genres' => $rareGenres,
            'obscure_genre' => $obscureGenre,
            'total_unique_genres' => $uniqueGenresCount,
            'total_genre_occurrences' => $totalOccurrences,
            'diversity_score' => $diversityScore,
        ];
    }
}

