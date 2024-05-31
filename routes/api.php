<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Jobs\SendUserConfirmationEmail;
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
        ->middleware(['Validation:login','Authorize']) // You can add 'CheckBlacklist' here if you really need it
        ->name('login');

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('blacklist')
        ->name('logout');

    Route::get('/user', [AuthController::class, 'user'])->middleware('blacklist');
    Route::apiResource('books', BookController::class)->middleware('blacklist');
});

Route::get('/confirm-email/{token}', [AuthController::class, 'confirmEmail'])->name('confirm.email');


// Route::group(['middleware' => ['api', 'blacklist']], function () {
//     Route::get('/books', [BookController::class, 'index'])->name('books.index');
//     Route::post('/create/books', [BookController::class, 'store'])->middleware('Validation:book')->name('books.store');
//     Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
//     Route::put('/books/{book}', [BookController::class, 'update'])->middleware('Validation:book')->name('books.update');
//     Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
// });

