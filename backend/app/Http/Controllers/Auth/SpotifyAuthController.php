<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\SpotifyApiException;
use App\Http\Controllers\Controller;
use App\Services\Spotify\SpotifyAuthService;
use App\Services\Spotify\SpotifyTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SpotifyAuthController extends Controller
{
    public function __construct(
        protected SpotifyAuthService $authService,
        protected SpotifyTokenService $tokenService
    ) {}

    /**
     * Start the Spotify OAuth Authorization Code flow.
     * Generates a cryptographically secure state parameter and returns
     * the Spotify authorization URL or redirects directly.
     */
    public function redirect(Request $request): JsonResponse|RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('spotify_oauth_state', $state);

        $authUrl = $this->authService->getAuthorizationUrl($state);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'auth_url' => $authUrl,
                'state' => $state,
            ]);
        }

        return redirect()->away($authUrl);
    }

    /**
     * Handle the Spotify OAuth callback.
     * Validates state, exchanges authorization code for tokens,
     * persists/updates the User, and authenticates the session.
     */
    public function callback(Request $request): RedirectResponse
    {
        $frontendUrl = rtrim(config('spotify.frontend_url', 'http://localhost:5173'), '/');

        // Check if user denied authorization or an error occurred
        if ($request->has('error')) {
            $error = $request->query('error');
            Log::warning('Autorização Spotify negada ou erro retornado', ['error' => $error]);

            return redirect("{$frontendUrl}/login?error=" . urlencode($error));
        }

        $code = $request->query('code');
        $state = $request->query('state');
        $storedState = $request->session()->pull('spotify_oauth_state');

        // Validate state parameter to protect against CSRF attacks
        if (empty($code) || empty($state) || $state !== $storedState) {
            Log::warning('Validação de state falhou no callback do Spotify OAuth', [
                'has_code' => !empty($code),
                'received_state' => $state,
                'had_stored_state' => !empty($storedState),
            ]);

            return redirect("{$frontendUrl}/login?error=invalid_state");
        }

        try {
            $tokenData = $this->authService->handleCallback($code);
            $user = $this->authService->getOrCreateUser($tokenData);

            Auth::login($user, remember: true);
            $request->session()->regenerate();

            Log::info('Usuário autenticado com sucesso via Spotify', [
                'user_id' => $user->id,
                'spotify_id' => $user->spotify_id,
            ]);

            return redirect("{$frontendUrl}/dashboard?auth=success");
        } catch (SpotifyApiException $e) {
            Log::error('Erro ao processar callback do Spotify: ' . $e->getMessage());
            return redirect("{$frontendUrl}/login?error=" . urlencode('Falha ao comunicar com Spotify'));
        } catch (\Exception $e) {
            Log::critical('Erro inesperado no callback do Spotify: ' . $e->getMessage());
            return redirect("{$frontendUrl}/login?error=" . urlencode('Erro interno na autenticação'));
        }
    }

    /**
     * Return the currently authenticated user's profile and Spotify connection status.
     */
    public function user(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'authenticated' => false,
                'user' => null,
            ]);
        }

        $hasValidToken = $this->tokenService->hasValidToken($user);

        return response()->json([
            'authenticated' => true,
            'has_spotify' => $hasValidToken,
            'user' => [
                'id' => $user->id,
                'spotify_id' => $user->spotify_id,
                'name' => $user->display_name ?: $user->name,
                'display_name' => $user->display_name ?: $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'country' => $user->country,
                'product' => $user->product,
                'last_login_at' => $user->last_login_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Disconnect the user's Spotify account, revoke stored tokens, and log out.
     */
    public function disconnect(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            // Revoke and remove tokens
            $this->tokenService->revokeTokens($user);

            // Invalidate user cache keys
            Cache::forget("user_dashboard_summary_{$user->id}");

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('Usuário desconectou a conta do Spotify com sucesso', [
                'user_id' => $user->id,
            ]);
        }

        return response()->json([
            'message' => 'Conta desconectada com sucesso.',
            'disconnected' => true,
        ]);
    }
}
