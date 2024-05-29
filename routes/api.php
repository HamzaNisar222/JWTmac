<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidationMiddleware;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('Validation:register')
        ->name('register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware(['Validation:login', 'Authorize'])
        ->name('login');
});


