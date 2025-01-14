<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function($data, $message="",$status=200){
            return response()->json([
               'status' =>'success',
               'message' => $message,
                'data' => $data,
            ], $status);
        } );

        Response::macro('error', function( $message="",$status=400){
            return response()->json([
               'status' =>'error',
               'message' => $message,
                // 'data' => $data,
            ], $status);
        } );

        Response::macro('notFound', function ($message = 'Resource not found') {
            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], 404);
        });


        Response::macro('error', function($data, $message="something went wrong",$status=500){
            return response()->json([
               'status' =>'error',
               'message' => $message,
                'data' => $data,
            ], $status);
        } );
    }
}
