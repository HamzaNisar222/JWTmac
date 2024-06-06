<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\ActiveToken;
use Illuminate\Http\Request;
use App\Models\BlacklistedToken;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class CheckBlacklistMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Retrieve the token from the request
            // dd($request->all());
            $token = JWTAuth::getToken();

            // Check if the token is not present
            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 400);
            }

            // Check if the token is blacklisted
            if (BlacklistedToken::where('token', $token)->exists()) {
                return response()->json(['error' => 'Token is blacklisted'], 401);
            }

            // Check if the token is expired
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // Token has expired
            $token = JWTAuth::getToken();
            ActiveToken::where('token', $token)->delete();

            return response()->json(['error' => 'Token has expired'], 401);
        }

        return $next($request);
    }
}
