protected $routeMiddleware = [
    // ... other middleware
    'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
];