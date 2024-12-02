<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            \Log::error($e->getMessage());
        });


        $this->renderable(function (Throwable $e, Request $request) {
            // 404、405、500 error會轉到首頁
            if (
                $e instanceof NotFoundHttpException ||
                $e instanceof MethodNotAllowedHttpException
            ){
                    return redirect()->route('index');
            }

            if ($e instanceof HttpException && $e->getStatusCode() === 500) {
                return redirect()->route('index');
            }
                // If the error is not handled, return null to let the default handler handle it
            return null;
        });

    }
}
