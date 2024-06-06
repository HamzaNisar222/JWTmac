<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogActionsMiddleware
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
        Log::channel('BookLogs')->info('Request:', [
            'method' => $request->method(),
            'url' => $request->url(),
            'parameters' => $request->all(),
        ]);

        // Proceed with the request
        $response = $next($request);

        // Log response information
        Log::channel('BookLogs')->info('Response:', [
            'status_code' => $response->status(),
            'content' => $response->getContent(),
        ]);

        return $response;
    }
}
