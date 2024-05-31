<?php

namespace App\Exceptions;

// use GuzzleHttp\Psr7\Response;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {

        if ($e instanceof AuthenticationException) {
            return Response::error('Unauthenticated', 'You are not authenticated', 401);
        }

        if ($e instanceof NotFoundHttpException) {
            return Response::notFound("Not Found");
        }

        // For all other exceptions, use the general error macro
        return Response::error($e->getMessage(), $e->getCode() ?: 500);
    }
}
