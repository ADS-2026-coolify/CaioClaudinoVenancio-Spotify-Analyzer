<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpotifyConnected
{
    /**
     * Handle an incoming request.
     * Ensure the user is logged in and has an active Spotify token record.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Não autenticado. Por favor, conecte-se com o Spotify.',
                'error' => 'unauthenticated',
            ], 401);
        }

        if (!$user->spotifyToken) {
            return response()->json([
                'message' => 'Conta do Spotify não conectada ou sessão expirada.',
                'error' => 'spotify_disconnected',
            ], 401);
        }

        return $next($request);
    }
}

