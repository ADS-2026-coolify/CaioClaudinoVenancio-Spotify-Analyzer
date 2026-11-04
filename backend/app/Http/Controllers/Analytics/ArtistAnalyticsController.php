<?php

namespace App\Http\Controllers\Analytics;

use App\Enums\TimeRange;
use App\Http\Controllers\Controller;
use App\Services\Spotify\SpotifyApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ArtistAnalyticsController extends Controller
{
    public function __construct(
        protected SpotifyApiService $spotifyApi
    ) {}

    /**
     * Get user's top artists for the selected time range.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'time_range' => ['nullable', 'string', Rule::enum(TimeRange::class)],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $timeRange = $validated['time_range'] ?? TimeRange::MediumTerm->value;
        $limit = (int) ($validated['limit'] ?? 50);

        $user = Auth::user();
        $response = $this->spotifyApi->getTopArtists($user, $timeRange, $limit);
        $items = $response['items'] ?? [];

        $totalPopularity = 0;
        $formattedArtists = [];

        foreach ($items as $index => $artist) {
            $popularity = (int) ($artist['popularity'] ?? 0);
            $totalPopularity += $popularity;

            $images = $artist['images'] ?? [];
            $imageUrl = $images[0]['url'] ?? null;
            $thumbnailUrl = end($images)['url'] ?? $imageUrl;

            $formattedArtists[] = [
                'rank' => $index + 1,
                'id' => $artist['id'],
                'name' => $artist['name'],
                'genres' => $artist['genres'] ?? [],
                'popularity' => $popularity,
                'followers' => $artist['followers']['total'] ?? 0,
                'image_url' => $imageUrl,
                'thumbnail_url' => $thumbnailUrl,
                'spotify_url' => $artist['external_urls']['spotify'] ?? null,
            ];
        }

        $count = count($formattedArtists);
        $avgPopularity = $count > 0 ? round($totalPopularity / $count, 1) : 0;

        return response()->json([
            'success' => true,
            'time_range' => $timeRange,
            'time_range_label' => TimeRange::from($timeRange)->label(),
            'total' => $count,
            'average_popularity' => $avgPopularity,
            'artists' => $formattedArtists,
        ]);
    }
}

