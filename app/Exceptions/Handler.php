<?php

namespace App\Exceptions;

// use GuzzleHttp\Psr7\Response;
use Throwable;
use Illuminate\Support\Facades\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

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
            return Response::error('Unauthenticated', 'You are not authenticated');
        }

        if ($e instanceof NotFoundHttpException) {
            return Response::notFound("Not Found");
        }
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return Response::notFound("Record Not Found");
        }
        // if ($e instanceof ValidationException) {
        //     return Response::error($e->getMessage(), $e->getCode() ?: 400);
        // }

        return parent::render($request, $e);
    }
}
