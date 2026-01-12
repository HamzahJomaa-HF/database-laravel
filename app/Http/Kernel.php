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
             \App\Http\Middleware\VerifyCsrfToken::class, // Critical for CSRF protection
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth.system' => \App\Http\Middleware\AuthSystem::class,  // Only your custom auth
        'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
    'signed' => \App\Http\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    
    // YOUR CUSTOM MIDDLEWARE
    'auth.employee' => \App\Http\Middleware\AuthenticateEmployee::class,
    'module.access' => \App\Http\Middleware\CheckModuleAccess::class,
    'resource.access' => \App\Http\Middleware\CheckResourceAccess::class,
    
    'role' => \App\Http\Middleware\CheckRole::class,
   
   
    ];
}