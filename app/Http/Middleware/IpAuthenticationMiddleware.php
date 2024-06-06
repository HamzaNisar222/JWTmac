<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IpAuthenticationMiddleware
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
        // dd($request->all());
        $allowedIP = '127.0.0.1'; // Replace this with the allowed IP address

        // Get the client's IP address
        $clientIP = $_SERVER['REMOTE_ADDR'];
        // dd($clientIP);

        // Check if the client's IP matches the allowed IP
        if ($clientIP !== $allowedIP) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
