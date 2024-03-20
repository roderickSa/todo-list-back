<?php

namespace App\Exceptions;

use App\Http\Resources\ErrorResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Exception $exception, Request $request) {
            if ($request->is('api/*')) {
                if ($exception instanceof AuthenticationException) {
                    $error = new \Exception($exception->getMessage(), Response::HTTP_UNAUTHORIZED);

                    return (new ErrorResource($error))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);
                }

                if ($exception instanceof NotFoundHttpException) {
                    $error = new \Exception($exception->getMessage(), Response::HTTP_BAD_REQUEST);

                    return new ErrorResource($error);
                }
            }
        });
    }
}
