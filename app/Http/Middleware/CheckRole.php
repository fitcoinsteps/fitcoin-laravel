<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth('api')->user();
        if (!$user || !$user->roles()->whereIn('slug', $roles)->exists()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}