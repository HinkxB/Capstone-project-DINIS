protected $middlewareGroups = [
        // ...
        'api' => [
            \App\Http\Middleware\SecurityHeaders::class, // <-- Add this here
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
