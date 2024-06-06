<?php
namespace App\Http\Controllers\Api;


use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendUserConfirmation;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\URL;
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
        $token = Str::random(32);

        // Store the token in the database
        $user->email_confirmation_token = $token;
        $user->save();
        // Generate signed URL
        $confirmationUrl = URL::temporarySignedRoute(
            'confirm.email',
            now()->addMinutes(5),
            ['token' => $token]
        );

        SendUserConfirmationEmail::dispatch($user, $confirmationUrl);

        return Response::success($user, "User registration successful. Please check your email for confirmation.", 201);
    }

    public function confirmEmail(Request $request)
    {
     // Check if the signed URL is valid
     if (! $request->hasValidSignature()) {
        return Response::error("Invalid or expired email confirmation link.", 400);
    }

    $token = $request->token;

    // Find the user by the token
    $user = User::where('email_confirmation_token', $token)->first();

    if (! $user) {
        return Response::error("Invalid email confirmation token.", 400);
    }

    // Confirm the user's email
    $user->email_verified_at = now();
    $user->email_confirmation_token = null;
    $user->status=1; // Clear the token
    $user->save();

    return Response::success(null, "Email confirmed successfully.", 200);
    }


    public function login(Request $request)
    {
        $user = $request->user;
        $remember = $request->remember_me;
        if (!$user) {
            return Response::error("Invalid Credentials", 404);
        }
        $token=$user->createToken($remember);
        $expiresIn = $remember ? 30 * 24 * 60 * 60 : JWTAuth::factory()->getTTL() * 60;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiresIn,
        ]);
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
