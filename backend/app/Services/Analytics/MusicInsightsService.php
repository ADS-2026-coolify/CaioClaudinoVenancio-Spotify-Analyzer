<?php

namespace App\Services\Analytics;

use App\Models\User;
use App\Services\Spotify\SpotifyApiService;

class MusicInsightsService
{
    public function __construct(
        protected SpotifyApiService $spotifyApi,
        protected GenreAnalysisService $genreService,
        protected DecadeAnalysisService $decadeService
    ) {}

    /**
     * Generate deterministic insights based strictly on user's real listening patterns.
     *
     * @return array<int, array{
     *     category: string,
     *     tag: string,
     *     title: string,
     *     description: string,
     *     icon: string
     * }>
     */
    public function getInsights(User $user): array
    {
        $insights = [];

        // 1. Fetch top artists, tracks, and recently played
        $artistsData = $this->spotifyApi->getTopArtists($user, 'medium_term', 50);
        $tracksData = $this->spotifyApi->getTopTracks($user, 'medium_term', 50);
        $recentData = $this->spotifyApi->getRecentlyPlayed($user, 50);

        $artists = $artistsData['items'] ?? [];
        $tracks = $tracksData['items'] ?? [];
        $recentItems = $recentData['items'] ?? [];

        // 2. Fetch genre and decade analyses
        $genreAnalysis = $this->genreService->getGenreAnalysis($user, 'medium_term');
        $decadeAnalysis = $this->decadeService->getDecadeAnalysis($user, 'medium_term');

        // Insight: Top Genre Dominance
        if (!empty($genreAnalysis['top_genres'])) {
            $topGenre = $genreAnalysis['top_genres'][0];
            if ($topGenre['percentage'] >= 25.0) {
                $insights[] = [
                    'category' => 'genre',
                    'tag' => 'Gênero Forte',
                    'title' => 'Forte preferência por ' . $topGenre['name'],
                    'description' => "{$topGenre['percentage']}% dos gêneros associados aos seus artistas favoritos concentram-se em {$topGenre['name']}.",
                    'icon' => 'Flame',
                ];
            }
        }

        // Insight: Genre Diversity
        $diversity = $genreAnalysis['diversity_score'] ?? 0.0;
        if ($diversity >= 0.55 && $genreAnalysis['total_unique_genres'] >= 10) {
            $insights[] = [
                'category' => 'diversity',
                'tag' => 'Explorador',
                'title' => 'Explorador de Gêneros',
                'description' => "Você transita por {$genreAnalysis['total_unique_genres']} gêneros diferentes. Sua paleta musical é eclética e curiosa.",
                'icon' => 'Compass',
            ];
        } elseif ($diversity <= 0.25 && $genreAnalysis['total_unique_genres'] > 0) {
            $insights[] = [
                'category' => 'diversity',
                'tag' => 'Focado',
                'title' => 'Gosto Especializado',
                'description' => 'Você tem um gosto bem direcionado e sabe com clareza a identidade sonora que prefere ouvir.',
                'icon' => 'Target',
            ];
        }

        // Insight: Obscure Genre
        if (!empty($genreAnalysis['obscure_genre'])) {
            $obscure = $genreAnalysis['obscure_genre'];
            $insights[] = [
                'category' => 'rare_genre',
                'tag' => 'Descoberta',
                'title' => 'Seu Gênero Mais Obscuro',
                'description' => "Entre seus artistas analisados, você escuta \"{$obscure['name']}\", representado por {$obscure['artist_sample']}.",
                'icon' => 'Sparkles',
            ];
        }

        // Insight: Decade Preference
        if (!empty($decadeAnalysis['predominant_decade'])) {
            $predominantDecade = $decadeAnalysis['predominant_decade'];
            $decadeCount = count($decadeAnalysis['decades']);

            if (str_contains($predominantDecade, '2020')) {
                $insights[] = [
                    'category' => 'decade',
                    'tag' => 'Atual',
                    'title' => 'Sempre nas Novidades',
                    'description' => 'A maior parte das suas músicas mais ouvidas foi lançada nos Anos 2020. Você consome música contemporânea em tempo real.',
                    'icon' => 'Radio',
                ];
            } elseif (preg_match('/19\d0/', $predominantDecade)) {
                $insights[] = [
                    'category' => 'decade',
                    'tag' => 'Nostalgia',
                    'title' => 'Alma Nostálgica',
                    'description' => "Os {$predominantDecade} dominam suas faixas favoritas. Clássicos atemporais têm lugar garantido no seu dia a dia.",
                    'icon' => 'Disc',
                ];
            } else {
                $insights[] = [
                    'category' => 'decade',
                    'tag' => 'Época de Ouro',
                    'title' => "Fidelidade aos {$predominantDecade}",
                    'description' => "As produções dos {$predominantDecade} formam a espinha dorsal da sua biblioteca musical.",
                    'icon' => 'Disc',
                ];
            }
        }

        // Insight: Favorite Artist Devotion
        if (!empty($artists)) {
            $topArtist = $artists[0]['name'] ?? 'Artista';
            $insights[] = [
                'category' => 'artist',
                'tag' => 'Artista Favorito',
                'title' => "{$topArtist} no Topo",
                'description' => "{$topArtist} ocupa o 1º lugar no seu ranking pessoal dos últimos 6 meses.",
                'icon' => 'Crown',
            ];
        }

        // Insight: Recently Played Habit
        if (!empty($recentItems)) {
            $recentArtists = [];
            foreach ($recentItems as $item) {
                $trackArtists = $item['track']['artists'] ?? [];
                foreach ($trackArtists as $a) {
                    $recentArtists[$a['name']] = true;
                }
            }

            $uniqueRecentCount = count($recentArtists);
            $totalRecent = count($recentItems);

            if ($uniqueRecentCount >= 18) {
                $insights[] = [
                    'category' => 'listening_habit',
                    'tag' => 'Hábito Recente',
                    'title' => 'Sessões Dinâmicas',
                    'description' => "Nas suas últimas {$totalRecent} reproduções, você ouviu {$uniqueRecentCount} artistas distintos sem se prender a repetições.",
                    'icon' => 'Shuffle',
                ];
            } elseif ($uniqueRecentCount <= 7 && $totalRecent >= 15) {
                $insights[] = [
                    'category' => 'listening_habit',
                    'tag' => 'Hábito Recente',
                    'title' => 'Em Modo Repetição',
                    'description' => "Nas suas últimas reproduções você concentrou suas atenções em apenas {$uniqueRecentCount} artistas, mergulhando a fundo em suas discografias.",
                    'icon' => 'Repeat',
                ];
            }
        }

        // Summary statement
        $totalArtists = count($artists);
        $totalTracks = count($tracks);
        $insights[] = [
            'category' => 'summary',
            'tag' => 'Base de Dados',
            'title' => 'Análise Completa',
            'description' => "Diagnóstico gerado a partir de {$totalArtists} artistas e {$totalTracks} faixas analisadas diretamente da sua conta Spotify.",
            'icon' => 'Activity',
        ];

        return $insights;
    }
}
