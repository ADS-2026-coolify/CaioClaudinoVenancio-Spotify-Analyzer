<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Services\Spotify\SpotifyApiService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecentlyPlayedController extends Controller
{
    public function __construct(
        protected SpotifyApiService $spotifyApi
    ) {}

    /**
     * Get user's recently played tracks from Spotify.
     * Note: Spotify API provides a limited window of the most recent plays (up to 50),
     * not complete lifetime play history.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = min(50, max(1, (int) $request->query('limit', 50)));

        $user = Auth::user();
        $response = $this->spotifyApi->getRecentlyPlayed($user, $limit);
        $items = $response['items'] ?? [];

        $formattedPlays = [];
        foreach ($items as $item) {
            $track = $item['track'] ?? [];
            if (empty($track)) {
                continue;
            }

            $playedAt = $item['played_at'] ?? null;
            $playedAtCarbon = $playedAt ? Carbon::parse($playedAt) : null;

            $artistNames = array_column($track['artists'] ?? [], 'name');
            $images = $track['album']['images'] ?? [];

            $durationMs = (int) ($track['duration_ms'] ?? 0);
            $minutes = floor($durationMs / 60000);
            $seconds = floor(($durationMs % 60000) / 1000);

            $formattedPlays[] = [
                'id' => $track['id'] ?? '',
                'name' => $track['name'] ?? 'Música',
                'artists' => implode(', ', $artistNames) ?: 'Artista',
                'album' => $track['album']['name'] ?? '',
                'played_at' => $playedAt,
                'played_at_human' => $playedAtCarbon?->diffForHumans(),
                'duration_formatted' => sprintf('%d:%02d', $minutes, $seconds),
                'image_url' => $images[0]['url'] ?? null,
                'thumbnail_url' => end($images)['url'] ?? ($images[0]['url'] ?? null),
                'spotify_url' => $track['external_urls']['spotify'] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'notice' => 'O Spotify disponibiliza uma janela limitada das reproduções mais recentes (últimas faixas tocadas), não o histórico vitalício completo.',
            'total' => count($formattedPlays),
            'tracks' => $formattedPlays,
        ]);
    }
}
