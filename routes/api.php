<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;


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
        ->middleware(['Validation:login','Authorize']) // You can add 'CheckBlacklist' here if you really need it
        ->name('login');

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('blacklist')
        ->name('logout');

    Route::apiResource('books', BookController::class)->middleware(['blacklist','verifyip','Log','CustomThrottle:2,1']);
    // Route::put('/books/update/{id}', [BookController::class, 'update'])->middleware(['blacklist','verifyip','Log']);
});

Route::get('/confirm-email/{token}', [AuthController::class, 'confirmEmail'])->name('confirm.email');



