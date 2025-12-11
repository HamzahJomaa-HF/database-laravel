<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        // Keep only essential global middleware
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // Only what you NEED for sessions
            \Illuminate\Session\Middleware\StartSession::class,      // Essential for login
            \Illuminate\View\Middleware\ShareErrorsFromSession::class, // For validation errors
            \Illuminate\Routing\Middleware\SubstituteBindings::class,  // For route binding
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth.system' => \App\Http\Middleware\AuthSystem::class,  // Only your custom auth
    ];
}