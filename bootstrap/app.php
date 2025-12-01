<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect unauthenticated users to admin login for admin routes
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            // Use redirectTo from exception if available, otherwise determine based on route
            $redirectTo = $e->redirectTo($request);
            
            if ($redirectTo) {
                return redirect($redirectTo)
                    ->with('error', 'Please login to access the admin panel.');
            }
            
            // Fallback: Check if request is for admin routes
            $path = $request->path();
            if (str_starts_with($path, 'admin')) {
                return redirect()->route('admin.login')
                    ->with('error', 'Please login to access the admin panel.');
            }
            
            // Default redirect for other routes
            return redirect()->route('home');
        });
    })->create();
