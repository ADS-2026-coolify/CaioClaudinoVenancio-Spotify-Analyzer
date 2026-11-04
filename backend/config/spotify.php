<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Spotify API Credentials
    |--------------------------------------------------------------------------
    |
    | Credenciais do aplicativo registrado no Spotify Developer Dashboard.
    | Obtenha em: https://developer.spotify.com/dashboard
    |
    */

    'client_id' => env('SPOTIFY_CLIENT_ID', ''),

    'client_secret' => env('SPOTIFY_CLIENT_SECRET', ''),

    'redirect_uri' => env('SPOTIFY_REDIRECT_URI', 'http://localhost:8000/api/spotify/callback'),

    /*
    |--------------------------------------------------------------------------
    | Spotify OAuth Scopes
    |--------------------------------------------------------------------------
    |
    | Permissões solicitadas ao usuário durante a autorização.
    |
    */

    'scopes' => [
        'user-read-private',
        'user-read-email',
        'user-top-read',
        'user-read-recently-played',
        'playlist-modify-public',
        'playlist-modify-private',
    ],

    /*
    |--------------------------------------------------------------------------
    | Spotify API URLs
    |--------------------------------------------------------------------------
    |
    | URLs base para a API e autenticação do Spotify.
    |
    */

    'api_base_url' => 'https://api.spotify.com/v1',

    'auth_url' => 'https://accounts.spotify.com/authorize',

    'token_url' => 'https://accounts.spotify.com/api/token',

    /*
    |--------------------------------------------------------------------------
    | Frontend URL
    |--------------------------------------------------------------------------
    |
    | URL do frontend para redirecionamento após autenticação.
    |
    */

    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),

    /*
    |--------------------------------------------------------------------------
    | Cache & Token Settings
    |--------------------------------------------------------------------------
    |
    | Configurações de cache para dados da API e refresh de tokens.
    |
    */

    'cache_ttl' => env('SPOTIFY_CACHE_TTL', 3600), // 1 hora em segundos

    'token_refresh_buffer' => 5, // Minutos antes da expiração para renovar o token

    'ssl_verify' => env('SPOTIFY_SSL_VERIFY', false),

];
