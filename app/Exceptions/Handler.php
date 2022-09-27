<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Response;
use App\Http\Resources\ErrorResource;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            abort(new ErrorResource(Response::HTTP_METHOD_NOT_ALLOWED, __('errors.method_not_allowed')));
        }
        if ($exception instanceof NotFoundHttpException) {
            abort(new ErrorResource(Response::HTTP_NOT_FOUND, __('errors.route_not_found')));
        }
        if ($exception instanceof ModelNotFoundException) {
            abort(new ErrorResource(Response::HTTP_NOT_FOUND, __('errors.model_not_found')));
        }
        if (
            $exception instanceof UnauthorizedException
            || $exception instanceof AuthorizationException
        ) {
            abort(new ErrorResource(Response::HTTP_FORBIDDEN, __('errors.unauthorized')));
        }

        if ($exception instanceof BadRequestHttpException) {
            abort(new ErrorResource(Response::HTTP_BAD_REQUEST, $exception->getMessage()));
        }
        return parent::render($request, $exception);
    }
}
