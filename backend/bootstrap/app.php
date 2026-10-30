<?php

use App\Exceptions\SpotifyApiException;
use App\Http\Middleware\EnsureSpotifyConnected;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'spotify.connected' => EnsureSpotifyConnected::class,
        ]);

        // Exclude API routes from CSRF token verification (SPA uses token/cookie auth or session)
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'spotify/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Centralized handling for Spotify API errors
        $exceptions->render(function (SpotifyApiException $e, Request $request) {
            $statusCode = $e->statusCode >= 400 && $e->statusCode <= 599 ? $e->statusCode : 500;

            return response()->json([
                'success' => false,
                'error' => $e->spotifyError ?: 'spotify_api_error',
                'message' => $e->spotifyMessage ?: $e->getMessage(),
                'retry_after' => $e->getRetryAfter(),
            ], $statusCode);
        });
    })->create();
