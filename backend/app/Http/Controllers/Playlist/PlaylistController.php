<?php

namespace App\Http\Controllers\Playlist;

use App\Enums\MoodCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePlaylistRequest;
use App\Http\Requests\GeneratePlaylistRequest;
use App\Models\GeneratedPlaylist;
use App\Services\Playlist\PlaylistGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    public function __construct(
        protected PlaylistGeneratorService $playlistService
    ) {}

    /**
     * Generate a preview of tracks tailored to a chosen mood and the user's music tastes.
     */
    public function generate(GeneratePlaylistRequest $request): JsonResponse
    {
        $user = Auth::user();
        $mood = MoodCategory::from($request->validated('mood'));
        $trackCount = (int) ($request->validated('track_count') ?? 20);

        $preview = $this->playlistService->generatePlaylist($user, $mood, $trackCount);

        return response()->json([
            'success' => true,
            'message' => 'Prévia de playlist gerada com sucesso!',
            'data' => $preview,
        ]);
    }

    /**
     * Confirm and save the playlist directly into the user's Spotify account.
     */
    public function create(CreatePlaylistRequest $request): JsonResponse
    {
        $user = Auth::user();
        $name = $request->validated('name');
        $description = $request->validated('description') ?? 'Gerado por Spotify Profile Analyzer';
        $isPublic = (bool) ($request->validated('is_public') ?? true);
        $mood = MoodCategory::from($request->validated('mood'));
        $trackUris = $request->validated('track_uris');

        try {
            $playlist = $this->playlistService->createSpotifyPlaylist(
                $user,
                $name,
                $description,
                $isPublic,
                $mood,
                $trackUris
            );

            return response()->json([
                'success' => true,
                'message' => 'Playlist criada com sucesso na sua conta do Spotify!',
                'playlist' => [
                    'id' => $playlist->id,
                    'spotify_playlist_id' => $playlist->spotify_playlist_id,
                    'name' => $playlist->name,
                    'description' => $playlist->description,
                    'mood' => $playlist->mood->value,
                    'mood_label' => $playlist->mood->label(),
                    'track_count' => $playlist->track_count,
                    'is_public' => $playlist->is_public,
                    'spotify_url' => $playlist->spotify_url,
                    'created_at' => $playlist->created_at->toISOString(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar playlist no Spotify: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all playlists created by the user through this application.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $playlists = GeneratedPlaylist::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'playlists' => $playlists->items(),
            'total' => $playlists->total(),
            'current_page' => $playlists->currentPage(),
            'last_page' => $playlists->lastPage(),
        ]);
    }
}

