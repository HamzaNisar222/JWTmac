<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;


class ThrottleMiddleware
{
    protected $limiter;


    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next, $maxAttempts = 2, $decayMinutes = 1)
    {
        // dd($request->all());
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json(['message' => 'Too Many Attempts.'], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60); // Convert decayMinutes to seconds

        $response = $next($request);

        return $response;
    }

    protected function resolveRequestSignature($request)
    {
        return sha1($request->method() . '|' . $request->route()->getDomain() . '|' . $request->ip());
    }
}
