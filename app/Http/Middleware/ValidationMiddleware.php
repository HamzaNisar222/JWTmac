<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\BookRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class ValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $key = null)
    {


        if ($key === 'register') {
            app(RegisterRequest::class);
        }
        if ($key === 'login') {

            app(LoginRequest::class);

        }
        if($key=='book'){
            //  dd($request->all());
             app(BookRequest::class);
        }



        return $next($request);
    }
}
