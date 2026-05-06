protected $routeMiddleware = [
    // default Laravel
    'auth' => \App\Http\Middleware\Authenticate::class,

    // custom
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];