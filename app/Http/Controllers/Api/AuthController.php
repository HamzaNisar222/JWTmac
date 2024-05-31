<?php
namespace App\Http\Controllers\Api;


use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendUserConfirmation;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendUserConfirmationEmail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::createUser($request->all());
        $expirationTime = now()->addMinutes(5);
        $tokenData = [
            'email' => $user->email,
            'expiration' => $expirationTime->timestamp,
        ];
        $token = base64_encode(json_encode($tokenData));
        $confirmationUrl = route('confirm.email', ['token' => $token]);

        SendUserConfirmationEmail::dispatch($user, $confirmationUrl);
        Artisan::call('queue:work --stop-when-empty');

        return Response::success($user, "User registration successful. Please check your email for confirmation.", 201);
    }

    public function confirmEmail($token)
    {
        // Decode the token
        $decodedToken = json_decode(base64_decode($token), true);

        if (!$decodedToken || !isset($decodedToken['email'], $decodedToken['expiration'])) {
            return response()->json(['message' => 'Invalid confirmation link.'], 404);
        }

        // Validate expiration time
        $expirationTime = $decodedToken['expiration'];
        if (now()->timestamp > $expirationTime) {
            return response()->json(['message' => 'Confirmation link has expired.'], 400);
        }

        // Proceed with email confirmation
        $email = $decodedToken['email'];
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->status = 1;
        $user->save();

        return response()->json(['message' => 'Email confirmed successfully.'], 200);
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
