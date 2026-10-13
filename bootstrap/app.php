<?php

use App\Http\Middleware\EnsureAuthenticatedEdgeDevice;
use App\Http\Middleware\EnsureCanManageEdgeDevices;
use App\Http\Middleware\EnsurePlatformIdempotency;
use App\Http\Middleware\EnsureTenantPluginIsActive;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\VerifyPlatformApiSignature;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webkul\Account\Exceptions\MissingJournalException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'platform.idempotency' => EnsurePlatformIdempotency::class,
            'platform.signature'   => VerifyPlatformApiSignature::class,
            'tenant.plugin'        => EnsureTenantPluginIsActive::class,
            'edge.device'          => EnsureAuthenticatedEdgeDevice::class,
            'edge.admin'           => EnsureCanManageEdgeDevices::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (MissingJournalException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }

            Notification::make()
                ->title(__('accounts::system.move.no-journal-found-title'))
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            return back();
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                if ($statusCode === 500) {
                    return response()->json([
                        'message' => app()->environment('production')
                            ? 'Server error occurred.'
                            : $e->getMessage(),
                        'exception' => app()->environment('production') ? null : get_class($e),
                        'file'      => app()->environment('production') ? null : $e->getFile(),
                        'line'      => app()->environment('production') ? null : $e->getLine(),
                    ], 500);
                }
            }
        });
    })->create();
