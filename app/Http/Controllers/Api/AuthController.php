<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $user=User::createUser($request->all());

        return Response::success($user,"provider User registration successful",201);
    }

    public function login(Request $request)
    {
        $user = $request->user;
        if (!$user) {
          return Response::error("Invalid Credentials",404);
        }

        $token = $user->createToken();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,

        ]);
    }

    public function user(){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        return response()->json(['status' => 'success', 'user' => $user], 200);
    }
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::parseToken()->authenticate(); // Retrieve authenticated user
            // dd($user);

            JWTAuth::invalidate($token);
            $user->blacklistToken($token);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to log out. Please try again later.',
            ], 500);
        }
    }



}
