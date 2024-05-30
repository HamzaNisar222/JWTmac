<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\BlacklistedToken;

class CheckBlacklistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Retrieve the token from the request
        $token = JWTAuth::getToken();

        // Check if the token is not present
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 400);
        }

        // Check if the token is blacklisted
        if (BlacklistedToken::where('token', $token)->exists()) {
            return response()->json(['error' => 'Token is blacklisted'], 401);
        }

        return $next($request);
    }
}
