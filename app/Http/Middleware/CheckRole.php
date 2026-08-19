<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        /** @var User|null $user */
        $user = null;

        // Check web guard first
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
        }
        // Then check API guard
        elseif (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
        }

        if (!$user) {
            if (!$request->expectsJson()) {
                return redirect()->route('login');
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Roles are lazy-loaded when accessed, no need for explicit load()
        $hasRole = false;
        foreach ($user->roles as $userRole) {
            if (in_array($userRole->slug, $roles)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            if (!$request->expectsJson()) {
                abort(403, 'Unauthorized access. Admin privileges required.');
            }
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You do not have the required permissions to access this resource'
            ], 403);
        }

        return $next($request);
    }
}