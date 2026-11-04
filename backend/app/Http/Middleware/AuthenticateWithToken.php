<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithToken
{
    /**
     * Authenticate the user from a Bearer or X-Auth-Token header if session is not present.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $token = $request->bearerToken() ?: $request->header('X-Auth-Token');

            if ($token && $userId = Cache::get("auth_token_{$token}")) {
                $user = User::find($userId);
                if ($user) {
                    Auth::login($user);
                }
            }
        }

        return $next($request);
    }
}

