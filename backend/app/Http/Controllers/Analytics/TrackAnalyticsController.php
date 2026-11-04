<?php

namespace App\Http\Controllers\Analytics;

use App\Enums\TimeRange;
use App\Http\Controllers\Controller;
use App\Services\Spotify\SpotifyApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TrackAnalyticsController extends Controller
{
    public function __construct(
        protected SpotifyApiService $spotifyApi
    ) {}

    /**
     * Get user's top tracks for the selected time range.
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
        $response = $this->spotifyApi->getTopTracks($user, $timeRange, $limit);
        $items = $response['items'] ?? [];

        $totalPopularity = 0;
        $formattedTracks = [];

        foreach ($items as $index => $track) {
            $popularity = (int) ($track['popularity'] ?? 0);
            $totalPopularity += $popularity;

            $releaseDate = $track['album']['release_date'] ?? null;
            $decade = null;
            if ($releaseDate && preg_match('/^(\d{4})/', $releaseDate, $m)) {
                $year = (int) $m[1];
                $decade = floor($year / 10) * 10 . 's';
            }

            $durationMs = (int) ($track['duration_ms'] ?? 0);
            $minutes = floor($durationMs / 60000);
            $seconds = floor(($durationMs % 60000) / 1000);
            $formattedDuration = sprintf('%d:%02d', $minutes, $seconds);

            $artistNames = array_column($track['artists'] ?? [], 'name');
            $images = $track['album']['images'] ?? [];

            $formattedTracks[] = [
                'rank' => $index + 1,
                'id' => $track['id'],
                'uri' => $track['uri'] ?? '',
                'name' => $track['name'],
                'artists' => implode(', ', $artistNames) ?: 'Artista',
                'album' => $track['album']['name'] ?? '',
                'release_date' => $releaseDate,
                'decade' => $decade,
                'popularity' => $popularity,
                'duration_ms' => $durationMs,
                'duration_formatted' => $formattedDuration,
                'image_url' => $images[0]['url'] ?? null,
                'thumbnail_url' => end($images)['url'] ?? ($images[0]['url'] ?? null),
                'spotify_url' => $track['external_urls']['spotify'] ?? null,
            ];
        }

        $count = count($formattedTracks);
        $avgPopularity = $count > 0 ? round($totalPopularity / $count, 1) : 0;

        return response()->json([
            'success' => true,
            'time_range' => $timeRange,
            'time_range_label' => TimeRange::from($timeRange)->label(),
            'total' => $count,
            'average_popularity' => $avgPopularity,
            'tracks' => $formattedTracks,
        ]);
    }
}

