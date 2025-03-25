<?php

use App\Exceptions\EnhancedValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'sanctum.stateful' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'sanctum.abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'sanctum.ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'setlocale' => \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->api([
            'setlocale',
        ]);

        $middleware->prepend([
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->append([
            \App\Http\Middleware\SetGroupMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            $customException = new EnhancedValidationException(
                $exception->validator,
                $exception->response,
                $exception->errorBag
            );

            return $customException->render($request);
        });
    })->create();
