<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $user=User::createUser($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user,

        ], 201);
    }

    public function login(Request $request)
    {
        $user = $request->user;
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invalid credentials',
                'code' => 401,
            ], 401);
        }

        $token = JWTAuth::fromUser($user);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }


}
