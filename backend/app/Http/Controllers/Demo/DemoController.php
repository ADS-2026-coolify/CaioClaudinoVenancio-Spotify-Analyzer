<?php

namespace App\Http\Controllers\Demo;

use App\Enums\MoodCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DemoController extends Controller
{
    /**
     * Demo Dashboard with high quality fictional data.
     */
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'is_demo' => true,
            'success' => true,
            'data' => [
                'user' => [
                    'id' => 9999,
                    'spotify_id' => 'spotify_demo_user',
                    'name' => 'Maria Clara',
                    'display_name' => 'Maria Clara (Modo Demonstração)',
                    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=300&fit=crop&crop=faces',
                    'country' => 'BR',
                    'product' => 'premium',
                ],
                'greeting' => 'Boa tarde',
                'stats' => [
                    'total_artists_analyzed' => 48,
                    'total_tracks_analyzed' => 50,
                    'predominant_genre' => 'Indie Rock',
                    'predominant_decade' => 'Anos 2010',
                    'favorite_artist' => [
                        'id' => 'demo_artist_1',
                        'name' => 'Arctic Monkeys',
                        'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&h=300&fit=crop',
                        'popularity' => 88,
                        'genres' => ['Indie Rock', 'Garage Rock', 'Alternative Rock'],
                    ],
                    'top_track' => [
                        'id' => 'demo_track_1',
                        'name' => 'Do I Wanna Know?',
                        'artist' => 'Arctic Monkeys',
                        'album' => 'AM',
                        'image_url' => 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    'unique_genres_count' => 24,
                    'diversity_score' => 0.48,
                ],
                'top_artists_preview' => [
                    [
                        'rank' => 1,
                        'id' => 'demo_artist_1',
                        'name' => 'Arctic Monkeys',
                        'genres' => ['Indie Rock', 'Garage Rock'],
                        'popularity' => 88,
                        'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'rank' => 2,
                        'id' => 'demo_artist_2',
                        'name' => 'Tame Impala',
                        'genres' => ['Neo-Psychedelia', 'Indie Pop'],
                        'popularity' => 84,
                        'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'rank' => 3,
                        'id' => 'demo_artist_3',
                        'name' => 'Gilberto Gil',
                        'genres' => ['MPB', 'Tropicália', 'Samba'],
                        'popularity' => 74,
                        'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'rank' => 4,
                        'id' => 'demo_artist_4',
                        'name' => 'Dua Lipa',
                        'genres' => ['Dance Pop', 'Synthpop'],
                        'popularity' => 90,
                        'image_url' => 'https://images.unsplash.com/photo-1520523839898-5071282543e2?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'rank' => 5,
                        'id' => 'demo_artist_5',
                        'name' => 'Tim Maia',
                        'genres' => ['Soul Brasileiro', 'MPB', 'Funk'],
                        'popularity' => 78,
                        'image_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                ],
                'top_tracks_preview' => [
                    [
                        'rank' => 1,
                        'id' => 'demo_t1',
                        'name' => 'Do I Wanna Know?',
                        'artists' => 'Arctic Monkeys',
                        'album' => 'AM',
                        'release_date' => '2013-09-09',
                        'image_url' => 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'rank' => 2,
                        'id' => 'demo_t2',
                        'name' => 'The Less I Know The Better',
                        'artists' => 'Tame Impala',
                        'album' => 'Currents',
                        'release_date' => '2015-07-17',
                        'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'rank' => 3,
                        'id' => 'demo_t3',
                        'name' => 'Não Quero Dinheiro',
                        'artists' => 'Tim Maia',
                        'album' => 'Tim Maia 1971',
                        'release_date' => '1971-04-12',
                        'image_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'rank' => 4,
                        'id' => 'demo_t4',
                        'name' => 'Levitating',
                        'artists' => 'Dua Lipa',
                        'album' => 'Future Nostalgia',
                        'release_date' => '2020-03-27',
                        'image_url' => 'https://images.unsplash.com/photo-1520523839898-5071282543e2?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'rank' => 5,
                        'id' => 'demo_t5',
                        'name' => 'Aquele Abraço',
                        'artists' => 'Gilberto Gil',
                        'album' => 'Gilberto Gil 1969',
                        'release_date' => '1969-08-01',
                        'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                ],
                'top_genres_preview' => [
                    ['name' => 'Indie Rock', 'count' => 18, 'percentage' => 26.5],
                    ['name' => 'MPB', 'count' => 14, 'percentage' => 20.6],
                    ['name' => 'Neo-Psychedelia', 'count' => 9, 'percentage' => 13.2],
                    ['name' => 'Dance Pop', 'count' => 8, 'percentage' => 11.8],
                    ['name' => 'Soul Brasileiro', 'count' => 6, 'percentage' => 8.8],
                ],
                'decades_summary' => [
                    ['decade' => '1970s', 'label' => 'Anos 1970', 'count' => 6, 'percentage' => 12.0],
                    ['decade' => '1980s', 'label' => 'Anos 1980', 'count' => 4, 'percentage' => 8.0],
                    ['decade' => '1990s', 'label' => 'Anos 1990', 'count' => 5, 'percentage' => 10.0],
                    ['decade' => '2000s', 'label' => 'Anos 2000', 'count' => 9, 'percentage' => 18.0],
                    ['decade' => '2010s', 'label' => 'Anos 2010', 'count' => 18, 'percentage' => 36.0],
                    ['decade' => '2020s', 'label' => 'Anos 2020', 'count' => 8, 'percentage' => 16.0],
                ],
                'recently_played_preview' => [
                    [
                        'id' => 'demo_r1',
                        'name' => 'Fluorescent Adolescent',
                        'artists' => 'Arctic Monkeys',
                        'album' => 'Favourite Worst Nightmare',
                        'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&h=300&fit=crop',
                        'played_at' => now()->subMinutes(12)->toISOString(),
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                    [
                        'id' => 'demo_r2',
                        'name' => 'Borderline',
                        'artists' => 'Tame Impala',
                        'album' => 'The Slow Rush',
                        'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=300&h=300&fit=crop',
                        'played_at' => now()->subMinutes(45)->toISOString(),
                        'spotify_url' => 'https://open.spotify.com',
                    ],
                ],
                'insights_preview' => [
                    [
                        'category' => 'genre',
                        'tag' => 'Gênero Forte',
                        'title' => 'Forte preferência por Indie Rock',
                        'description' => '26.5% dos gêneros associados aos seus artistas favoritos concentram-se em Indie Rock.',
                        'icon' => 'Flame',
                    ],
                    [
                        'category' => 'decade',
                        'tag' => 'Época de Ouro',
                        'title' => 'Fidelidade aos Anos 2010',
                        'description' => 'As produções dos Anos 2010 formam a espinha dorsal da sua biblioteca musical.',
                        'icon' => 'Disc',
                    ],
                    [
                        'category' => 'rare_genre',
                        'tag' => 'Descoberta',
                        'title' => 'Seu Gênero Mais Obscuro',
                        'description' => 'Entre seus artistas analisados, você escuta "Tropicália Psicodélica", representado por Os Mutantes.',
                        'icon' => 'Sparkles',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Demo Top Artists with time range support.
     */
    public function artists(Request $request): JsonResponse
    {
        $timeRange = $request->query('time_range', 'medium_term');

        $artists = [
            [
                'rank' => 1,
                'id' => 'art_1',
                'name' => 'Arctic Monkeys',
                'genres' => ['indie rock', 'garage rock', 'alternative rock'],
                'popularity' => 88,
                'followers' => 24500000,
                'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 2,
                'id' => 'art_2',
                'name' => 'Tame Impala',
                'genres' => ['neo-psychedelia', 'indie pop', 'psychedelic rock'],
                'popularity' => 84,
                'followers' => 9800000,
                'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 3,
                'id' => 'art_3',
                'name' => 'Gilberto Gil',
                'genres' => ['mpb', 'tropicalia', 'samba', 'bossa nova'],
                'popularity' => 74,
                'followers' => 2100000,
                'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 4,
                'id' => 'art_4',
                'name' => 'Dua Lipa',
                'genres' => ['dance pop', 'synthpop', 'pop'],
                'popularity' => 90,
                'followers' => 43200000,
                'image_url' => 'https://images.unsplash.com/photo-1520523839898-5071282543e2?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1520523839898-5071282543e2?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 5,
                'id' => 'art_5',
                'name' => 'Tim Maia',
                'genres' => ['soul brasileiro', 'mpb', 'funk carioca das antigas'],
                'popularity' => 78,
                'followers' => 3400000,
                'image_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 6,
                'id' => 'art_6',
                'name' => 'The Strokes',
                'genres' => ['indie rock', 'post-punk revival', 'garage rock'],
                'popularity' => 82,
                'followers' => 6500000,
                'image_url' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 7,
                'id' => 'art_7',
                'name' => 'Caetano Veloso',
                'genres' => ['mpb', 'tropicalia', 'bossa nova'],
                'popularity' => 76,
                'followers' => 2800000,
                'image_url' => 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 8,
                'id' => 'art_8',
                'name' => 'Daft Punk',
                'genres' => ['french touch', 'electronic', 'disco house'],
                'popularity' => 85,
                'followers' => 15200000,
                'image_url' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 9,
                'id' => 'art_9',
                'name' => 'Os Mutantes',
                'genres' => ['tropicalia psicodelica', 'psychedelic rock', 'mpb'],
                'popularity' => 61,
                'followers' => 450000,
                'image_url' => 'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 10,
                'id' => 'art_10',
                'name' => 'Radiohead',
                'genres' => ['art rock', 'alternative rock', 'melancholia'],
                'popularity' => 84,
                'followers' => 9100000,
                'image_url' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=300&h=300&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=100&h=100&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
        ];

        return response()->json([
            'is_demo' => true,
            'success' => true,
            'time_range' => $timeRange,
            'time_range_label' => 'Últimos 6 meses',
            'total' => count($artists),
            'average_popularity' => 79.2,
            'artists' => $artists,
        ]);
    }

    /**
     * Demo Top Tracks with decade categorization.
     */
    public function tracks(Request $request): JsonResponse
    {
        $timeRange = $request->query('time_range', 'medium_term');

        $tracks = [
            [
                'rank' => 1,
                'id' => 'trk_1',
                'name' => 'Do I Wanna Know?',
                'artists' => 'Arctic Monkeys',
                'album' => 'AM',
                'release_date' => '2013-09-09',
                'decade' => '2010s',
                'popularity' => 89,
                'duration_ms' => 272394,
                'duration_formatted' => '4:32',
                'image_url' => 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 2,
                'id' => 'trk_2',
                'name' => 'The Less I Know The Better',
                'artists' => 'Tame Impala',
                'album' => 'Currents',
                'release_date' => '2015-07-17',
                'decade' => '2010s',
                'popularity' => 87,
                'duration_ms' => 216320,
                'duration_formatted' => '3:36',
                'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 3,
                'id' => 'trk_3',
                'name' => 'Não Quero Dinheiro (Só Quero Amar)',
                'artists' => 'Tim Maia',
                'album' => 'Tim Maia 1971',
                'release_date' => '1971-04-12',
                'decade' => '1970s',
                'popularity' => 81,
                'duration_ms' => 153000,
                'duration_formatted' => '2:33',
                'image_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 4,
                'id' => 'trk_4',
                'name' => 'Levitating',
                'artists' => 'Dua Lipa',
                'album' => 'Future Nostalgia',
                'release_date' => '2020-03-27',
                'decade' => '2020s',
                'popularity' => 86,
                'duration_ms' => 203064,
                'duration_formatted' => '3:23',
                'image_url' => 'https://images.unsplash.com/photo-1520523839898-5071282543e2?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 5,
                'id' => 'trk_5',
                'name' => 'Aquele Abraço',
                'artists' => 'Gilberto Gil',
                'album' => 'Gilberto Gil 1969',
                'release_date' => '1969-08-01',
                'decade' => '1960s',
                'popularity' => 73,
                'duration_ms' => 324000,
                'duration_formatted' => '5:24',
                'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 6,
                'id' => 'trk_6',
                'name' => 'Last Nite',
                'artists' => 'The Strokes',
                'album' => 'Is This It',
                'release_date' => '2001-07-30',
                'decade' => '2000s',
                'popularity' => 80,
                'duration_ms' => 197000,
                'duration_formatted' => '3:17',
                'image_url' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 7,
                'id' => 'trk_7',
                'name' => 'Get Lucky',
                'artists' => 'Daft Punk ft. Pharrell Williams',
                'album' => 'Random Access Memories',
                'release_date' => '2013-05-17',
                'decade' => '2010s',
                'popularity' => 88,
                'duration_ms' => 369000,
                'duration_formatted' => '6:09',
                'image_url' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 8,
                'id' => 'trk_8',
                'name' => 'Sozinho',
                'artists' => 'Caetano Veloso',
                'album' => 'Prenda Minha',
                'release_date' => '1998-11-20',
                'decade' => '1990s',
                'popularity' => 79,
                'duration_ms' => 195000,
                'duration_formatted' => '3:15',
                'image_url' => 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 9,
                'id' => 'trk_9',
                'name' => 'A Minha Menina',
                'artists' => 'Os Mutantes',
                'album' => 'Os Mutantes',
                'release_date' => '1968-06-01',
                'decade' => '1960s',
                'popularity' => 65,
                'duration_ms' => 282000,
                'duration_formatted' => '4:42',
                'image_url' => 'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
            [
                'rank' => 10,
                'id' => 'trk_10',
                'name' => 'Karma Police',
                'artists' => 'Radiohead',
                'album' => 'OK Computer',
                'release_date' => '1997-05-21',
                'decade' => '1990s',
                'popularity' => 85,
                'duration_ms' => 264000,
                'duration_formatted' => '4:24',
                'image_url' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=300&h=300&fit=crop',
                'spotify_url' => 'https://open.spotify.com',
            ],
        ];

        return response()->json([
            'is_demo' => true,
            'success' => true,
            'time_range' => $timeRange,
            'time_range_label' => 'Últimos 6 meses',
            'total' => count($tracks),
            'average_popularity' => 81.7,
            'tracks' => $tracks,
        ]);
    }

    /**
     * Demo Genre Analysis with obscurity heuristic.
     */
    public function genres(): JsonResponse
    {
        return response()->json([
            'is_demo' => true,
            'success' => true,
            'time_range' => 'medium_term',
            'time_range_label' => 'Últimos 6 meses',
            'data' => [
                'genres' => [
                    ['name' => 'Indie Rock', 'count' => 18, 'percentage' => 26.5],
                    ['name' => 'MPB', 'count' => 14, 'percentage' => 20.6],
                    ['name' => 'Neo-Psychedelia', 'count' => 9, 'percentage' => 13.2],
                    ['name' => 'Dance Pop', 'count' => 8, 'percentage' => 11.8],
                    ['name' => 'Soul Brasileiro', 'count' => 6, 'percentage' => 8.8],
                    ['name' => 'Garage Rock', 'count' => 5, 'percentage' => 7.4],
                    ['name' => 'French Touch', 'count' => 3, 'percentage' => 4.4],
                    ['name' => 'Bossa Nova', 'count' => 2, 'percentage' => 2.9],
                    ['name' => 'Art Rock', 'count' => 2, 'percentage' => 2.9],
                    ['name' => 'Tropicália Psicodélica', 'count' => 1, 'percentage' => 1.5],
                ],
                'top_genres' => [
                    ['name' => 'Indie Rock', 'count' => 18, 'percentage' => 26.5],
                    ['name' => 'MPB', 'count' => 14, 'percentage' => 20.6],
                    ['name' => 'Neo-Psychedelia', 'count' => 9, 'percentage' => 13.2],
                    ['name' => 'Dance Pop', 'count' => 8, 'percentage' => 11.8],
                    ['name' => 'Soul Brasileiro', 'count' => 6, 'percentage' => 8.8],
                ],
                'rare_genres' => [
                    ['name' => 'Tropicália Psicodélica', 'count' => 1, 'percentage' => 1.5],
                    ['name' => 'Art Rock', 'count' => 2, 'percentage' => 2.9],
                    ['name' => 'Bossa Nova', 'count' => 2, 'percentage' => 2.9],
                    ['name' => 'French Touch', 'count' => 3, 'percentage' => 4.4],
                    ['name' => 'Garage Rock', 'count' => 5, 'percentage' => 7.4],
                ],
                'obscure_genre' => [
                    'name' => 'Tropicália Psicodélica',
                    'count' => 1,
                    'percentage' => 1.5,
                    'artist_sample' => 'Os Mutantes',
                ],
                'total_unique_genres' => 18,
                'total_genre_occurrences' => 68,
                'diversity_score' => 0.48,
            ],
        ]);
    }

    /**
     * Demo Decade Analysis.
     */
    public function decades(): JsonResponse
    {
        return response()->json([
            'is_demo' => true,
            'success' => true,
            'time_range' => 'medium_term',
            'time_range_label' => 'Últimos 6 meses',
            'data' => [
                'decades' => [
                    [
                        'decade' => '1960s',
                        'label' => 'Anos 1960',
                        'count' => 3,
                        'percentage' => 6.0,
                        'representative_track' => [
                            'id' => 'demo_t5',
                            'name' => 'Aquele Abraço',
                            'artist' => 'Gilberto Gil',
                            'album' => 'Gilberto Gil 1969',
                            'release_date' => '1969-08-01',
                            'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=300&h=300&fit=crop',
                            'spotify_url' => 'https://open.spotify.com',
                        ],
                    ],
                    [
                        'decade' => '1970s',
                        'label' => 'Anos 1970',
                        'count' => 6,
                        'percentage' => 12.0,
                        'representative_track' => [
                            'id' => 'demo_t3',
                            'name' => 'Não Quero Dinheiro (Só Quero Amar)',
                            'artist' => 'Tim Maia',
                            'album' => 'Tim Maia 1971',
                            'release_date' => '1971-04-12',
                            'image_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=300&h=300&fit=crop',
                            'spotify_url' => 'https://open.spotify.com',
                        ],
                    ],
                    [
                        'decade' => '1980s',
                        'label' => 'Anos 1980',
                        'count' => 4,
                        'percentage' => 8.0,
                        'representative_track' => [
                            'id' => 'demo_t_80s',
                            'name' => 'Tempo Perdido',
                            'artist' => 'Legião Urbana',
                            'album' => 'Dois',
                            'release_date' => '1986-07-20',
                            'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&h=300&fit=crop',
                            'spotify_url' => 'https://open.spotify.com',
                        ],
                    ],
                    [
                        'decade' => '1990s',
                        'label' => 'Anos 1990',
                        'count' => 5,
                        'percentage' => 10.0,
                        'representative_track' => [
                            'id' => 'demo_t10',
                            'name' => 'Karma Police',
                            'artist' => 'Radiohead',
                            'album' => 'OK Computer',
                            'release_date' => '1997-05-21',
                            'image_url' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=300&h=300&fit=crop',
                            'spotify_url' => 'https://open.spotify.com',
                        ],
                    ],
                    [
                        'decade' => '2000s',
                        'label' => 'Anos 2000',
                        'count' => 9,
                        'percentage' => 18.0,
                        'representative_track' => [
                            'id' => 'demo_t6',
                            'name' => 'Last Nite',
                            'artist' => 'The Strokes',
                            'album' => 'Is This It',
                            'release_date' => '2001-07-30',
                            'image_url' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?w=300&h=300&fit=crop',
                            'spotify_url' => 'https://open.spotify.com',
                        ],
                    ],
                    [
                        'decade' => '2010s',
                        'label' => 'Anos 2010',
                        'count' => 18,
                        'percentage' => 36.0,
                        'representative_track' => [
                            'id' => 'demo_t1',
                            'name' => 'Do I Wanna Know?',
                            'artist' => 'Arctic Monkeys',
                            'album' => 'AM',
                            'release_date' => '2013-09-09',
                            'image_url' => 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=300&h=300&fit=crop',
                            'spotify_url' => 'https://open.spotify.com',
                        ],
                    ],
                    [
                        'decade' => '2020s',
                        'label' => 'Anos 2020',
                        'count' => 8,
                        'percentage' => 16.0,
                        'representative_track' => [
                            'id' => 'demo_t4',
                            'name' => 'Levitating',
                            'artist' => 'Dua Lipa',
                            'album' => 'Future Nostalgia',
                            'release_date' => '2020-03-27',
                            'image_url' => 'https://images.unsplash.com/photo-1520523839898-5071282543e2?w=300&h=300&fit=crop',
                            'spotify_url' => 'https://open.spotify.com',
                        ],
                    ],
                ],
                'predominant_decade' => 'Anos 2010',
                'total_tracks_analyzed' => 50,
            ],
        ]);
    }

    /**
     * Demo Recently Played.
     */
    public function recentlyPlayed(): JsonResponse
    {
        return response()->json([
            'is_demo' => true,
            'success' => true,
            'notice' => 'Modo Demonstração: reproduções simuladas.',
            'total' => 5,
            'tracks' => [
                [
                    'id' => 'demo_r1',
                    'name' => 'Fluorescent Adolescent',
                    'artists' => 'Arctic Monkeys',
                    'album' => 'Favourite Worst Nightmare',
                    'played_at' => now()->subMinutes(12)->toISOString(),
                    'played_at_human' => 'há 12 minutos',
                    'duration_formatted' => '3:03',
                    'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&h=300&fit=crop',
                    'spotify_url' => 'https://open.spotify.com',
                ],
                [
                    'id' => 'demo_r2',
                    'name' => 'Borderline',
                    'artists' => 'Tame Impala',
                    'album' => 'The Slow Rush',
                    'played_at' => now()->subMinutes(45)->toISOString(),
                    'played_at_human' => 'há 45 minutos',
                    'duration_formatted' => '3:57',
                    'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=300&h=300&fit=crop',
                    'spotify_url' => 'https://open.spotify.com',
                ],
                [
                    'id' => 'demo_r3',
                    'name' => 'Palco',
                    'artists' => 'Gilberto Gil',
                    'album' => 'Luar (A Gente Precisa Ver o Luar)',
                    'played_at' => now()->subHours(2)->toISOString(),
                    'played_at_human' => 'há 2 horas',
                    'duration_formatted' => '4:26',
                    'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=300&h=300&fit=crop',
                    'spotify_url' => 'https://open.spotify.com',
                ],
                [
                    'id' => 'demo_r4',
                    'name' => 'One More Time',
                    'artists' => 'Daft Punk',
                    'album' => 'Discovery',
                    'played_at' => now()->subHours(5)->toISOString(),
                    'played_at_human' => 'há 5 horas',
                    'duration_formatted' => '5:20',
                    'image_url' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?w=300&h=300&fit=crop',
                    'spotify_url' => 'https://open.spotify.com',
                ],
                [
                    'id' => 'demo_r5',
                    'name' => 'Break My Heart',
                    'artists' => 'Dua Lipa',
                    'album' => 'Future Nostalgia',
                    'played_at' => now()->subHours(8)->toISOString(),
                    'played_at_human' => 'há 8 horas',
                    'duration_formatted' => '3:41',
                    'image_url' => 'https://images.unsplash.com/photo-1520523839898-5071282543e2?w=300&h=300&fit=crop',
                    'spotify_url' => 'https://open.spotify.com',
                ],
            ],
        ]);
    }

    /**
     * Demo Insights.
     */
    public function insights(): JsonResponse
    {
        return response()->json([
            'is_demo' => true,
            'success' => true,
            'title' => 'Seu Perfil Musical',
            'subtitle' => 'Insights determinísticos gerados no modo demonstração.',
            'total' => 6,
            'insights' => [
                [
                    'category' => 'genre',
                    'tag' => 'Gênero Forte',
                    'title' => 'Forte preferência por Indie Rock',
                    'description' => '26.5% dos gêneros associados aos seus artistas favoritos concentram-se em Indie Rock.',
                    'icon' => 'Flame',
                ],
                [
                    'category' => 'diversity',
                    'tag' => 'Explorador',
                    'title' => 'Explorador de Gêneros',
                    'description' => 'Você transita por 18 gêneros diferentes. Sua paleta musical é eclética e curiosa.',
                    'icon' => 'Compass',
                ],
                [
                    'category' => 'rare_genre',
                    'tag' => 'Descoberta',
                    'title' => 'Seu Gênero Mais Obscuro',
                    'description' => 'Entre seus artistas analisados, você escuta "Tropicália Psicodélica", representado por Os Mutantes.',
                    'icon' => 'Sparkles',
                ],
                [
                    'category' => 'decade',
                    'tag' => 'Época de Ouro',
                    'title' => 'Fidelidade aos Anos 2010',
                    'description' => 'As produções dos Anos 2010 formam a espinha dorsal da sua biblioteca musical.',
                    'icon' => 'Disc',
                ],
                [
                    'category' => 'artist',
                    'tag' => 'Artista Favorito',
                    'title' => 'Arctic Monkeys no Topo',
                    'description' => 'Arctic Monkeys ocupa o 1º lugar no seu ranking pessoal dos últimos 6 meses.',
                    'icon' => 'Crown',
                ],
                [
                    'category' => 'summary',
                    'tag' => 'Base de Dados',
                    'title' => 'Análise Completa',
                    'description' => 'Diagnóstico gerado a partir de 48 artistas e 50 faixas analisadas do perfil.',
                    'icon' => 'Activity',
                ],
            ],
        ]);
    }

    /**
     * Demo Playlist generation simulation.
     */
    public function generatePlaylist(Request $request): JsonResponse
    {
        $moodValue = $request->input('mood', 'relax');
        $mood = MoodCategory::tryFrom($moodValue) ?? MoodCategory::Relax;

        return response()->json([
            'is_demo' => true,
            'success' => true,
            'message' => 'Prévia de playlist simulada (Modo Demonstração)',
            'data' => [
                'mood' => $mood->value,
                'mood_label' => $mood->label(),
                'mood_emoji' => $mood->emoji(),
                'total_tracks' => 10,
                'estimated_duration_minutes' => 38,
                'tracks' => [
                    [
                        'id' => 'demo_pl_1',
                        'uri' => 'spotify:track:demo1',
                        'name' => 'Breathe Deeper',
                        'artists' => 'Tame Impala',
                        'album' => 'The Slow Rush',
                        'release_date' => '2020-02-14',
                        'duration_ms' => 372000,
                        'duration_formatted' => '6:12',
                        'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                        'is_favorite_artist' => true,
                    ],
                    [
                        'id' => 'demo_pl_2',
                        'uri' => 'spotify:track:demo2',
                        'name' => 'Cornerstone',
                        'artists' => 'Arctic Monkeys',
                        'album' => 'Humbug',
                        'release_date' => '2009-08-19',
                        'duration_ms' => 197000,
                        'duration_formatted' => '3:17',
                        'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                        'is_favorite_artist' => true,
                    ],
                    [
                        'id' => 'demo_pl_3',
                        'uri' => 'spotify:track:demo3',
                        'name' => 'Drão',
                        'artists' => 'Gilberto Gil',
                        'album' => 'Um Banda Um',
                        'release_date' => '1982-05-10',
                        'duration_ms' => 210000,
                        'duration_formatted' => '3:30',
                        'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                        'is_favorite_artist' => true,
                    ],
                    [
                        'id' => 'demo_pl_4',
                        'uri' => 'spotify:track:demo4',
                        'name' => 'Something About Us',
                        'artists' => 'Daft Punk',
                        'album' => 'Discovery',
                        'release_date' => '2001-03-12',
                        'duration_ms' => 232000,
                        'duration_formatted' => '3:52',
                        'image_url' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?w=300&h=300&fit=crop',
                        'spotify_url' => 'https://open.spotify.com',
                        'is_favorite_artist' => false,
                    ],
                ],
            ],
        ]);
    }
}

