<?php

use App\Http\Controllers\Analytics\ArtistAnalyticsController;
use App\Http\Controllers\Analytics\DecadeAnalyticsController;
use App\Http\Controllers\Analytics\GenreAnalyticsController;
use App\Http\Controllers\Analytics\InsightsController;
use App\Http\Controllers\Analytics\RecentlyPlayedController;
use App\Http\Controllers\Analytics\TrackAnalyticsController;
use App\Http\Controllers\Auth\SpotifyAuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Demo\DemoController;
use App\Http\Controllers\Playlist\PlaylistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Spotify Profile Analyzer REST API Routes.
|
*/

// Session-based Authentication Endpoints (requires 'web' middleware for session store)
Route::middleware(['web'])->group(function () {
    Route::prefix('spotify')->group(function () {
        Route::get('/auth', [SpotifyAuthController::class, 'redirect'])->name('spotify.auth');
        Route::get('/callback', [SpotifyAuthController::class, 'callback'])->name('spotify.callback');
    });

    // Current User Session Status (returns { authenticated: false } if not logged in)
    Route::get('/user', [SpotifyAuthController::class, 'user'])->name('user.show');
});

// Demo Mode Endpoints (Accessible without Spotify account, stateless)
Route::prefix('demo')->group(function () {
    Route::get('/dashboard', [DemoController::class, 'dashboard'])->name('demo.dashboard');
    Route::prefix('analytics')->group(function () {
        Route::get('/artists', [DemoController::class, 'artists'])->name('demo.analytics.artists');
        Route::get('/tracks', [DemoController::class, 'tracks'])->name('demo.analytics.tracks');
        Route::get('/genres', [DemoController::class, 'genres'])->name('demo.analytics.genres');
        Route::get('/decades', [DemoController::class, 'decades'])->name('demo.analytics.decades');
        Route::get('/recently-played', [DemoController::class, 'recentlyPlayed'])->name('demo.analytics.recently-played');
        Route::get('/insights', [DemoController::class, 'insights'])->name('demo.analytics.insights');
    });
    Route::post('/playlists/generate', [DemoController::class, 'generatePlaylist'])->name('demo.playlists.generate');
});

// Authenticated & Spotify-Connected Endpoints (requires 'web', 'auth:web', and 'spotify.connected')
Route::middleware(['web', 'auth:web', 'spotify.connected'])->group(function () {
    // Disconnect Spotify Account
    Route::delete('/spotify/disconnect', [SpotifyAuthController::class, 'disconnect'])->name('spotify.disconnect');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Analytics Modules
    Route::prefix('analytics')->group(function () {
        Route::get('/artists', [ArtistAnalyticsController::class, 'index'])->name('analytics.artists');
        Route::get('/tracks', [TrackAnalyticsController::class, 'index'])->name('analytics.tracks');
        Route::get('/genres', [GenreAnalyticsController::class, 'index'])->name('analytics.genres');
        Route::get('/decades', [DecadeAnalyticsController::class, 'index'])->name('analytics.decades');
        Route::get('/recently-played', [RecentlyPlayedController::class, 'index'])->name('analytics.recently-played');
        Route::get('/insights', [InsightsController::class, 'index'])->name('analytics.insights');
    });

    // Playlist Generator
    Route::prefix('playlists')->group(function () {
        Route::post('/generate', [PlaylistController::class, 'generate'])->name('playlists.generate');
        Route::post('/create', [PlaylistController::class, 'create'])->name('playlists.create');
        Route::get('/', [PlaylistController::class, 'index'])->name('playlists.index');
    });
});
