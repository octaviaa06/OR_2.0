protected $routeMiddleware = [
    // ...
    'auth.check' => \App\Http\Middleware\CheckAuth::class,
    'role' => \App\Http\Middleware\CheckRole::class,
];