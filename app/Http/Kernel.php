protected $routeMiddleware = [
    // default Laravel
    'auth' => \App\Http\Middleware\Authenticate::class,

    // custom
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];

protected $middlewareGroups = [
    'web' => [
        // ...
        \App\Http\Middleware\CheckLelangStatus::class,
    ],
];