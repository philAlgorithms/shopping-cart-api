<?php

namespace App\Exceptions;

use App\Exceptions\Stores\VendorHasNoStoreException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

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
        // $this->reportable(function (VendorHasNoStoreException $e) {
        //     return $e;
        // });


        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Record not found.'
                ], 404);
            }
        });
        // $this->renderable(function (AuthenticationException $e, Request $request) {
        //     return response()->json([
        //         'message' => $e->getMessage()
        //     ], 401);
        // });
        // $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
        //     return response()->json([
        //         'message' => $e->getMessage()
        //     ], 403);
        // });
    }
}
