<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Get the authenticated user from the API guard
        /** @var User|null $user */
        $user = auth('api')->user();
        
        // If no user is authenticated, return unauthorized
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if the user has any of the required roles
        $hasRole = false;
        
        // Loop through the user's roles
        foreach ($user->roles as $userRole) {
            if (in_array($userRole->slug, $roles)) {
                $hasRole = true;
                break;
            }
        }

        // If user doesn't have the required role, return forbidden
        if (!$hasRole) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You do not have the required permissions to access this resource'
            ], 403);
        }

        // User has the required role, proceed with the request
        return $next($request);
    }
}