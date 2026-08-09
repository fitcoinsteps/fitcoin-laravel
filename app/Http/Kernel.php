protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\CheckRole::class,
    'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
];