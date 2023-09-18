<?php

namespace App\Exceptions;

use App\Exceptions\ServicesActionExceptions\BadRequestException;
use App\Exceptions\ServicesActionExceptions\DictionaryExceptions;
use App\Exceptions\ServicesActionExceptions\InsuranceExceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $levels = [
        OcrException::class => LogLevel::NOTICE,
    ];
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
        $this->renderable(function (\Exception $e) {
            $allow = true;
            if (request()->route()) {
                $action = request()->route()->getAction();
                $allow  = $action && isset($action['prefix']) && $action['prefix'] == 'api';
            }

            if ($e instanceof OcrException && $allow) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'data' => []
                ], Response::HTTP_CONFLICT);
            } elseif ($e instanceof UnauthorizedException && $allow) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'data' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                ], Response::HTTP_UNAUTHORIZED);
            }
        });
    }
}
