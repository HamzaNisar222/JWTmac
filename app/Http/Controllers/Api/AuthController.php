<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
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

        $token = JWTAuth::fromUser($user);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,

        ]);
    }


}
