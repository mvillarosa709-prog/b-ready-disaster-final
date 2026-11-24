<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request checking for specific permission.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated. Please contact the administrator.']);
        }

        if (! $user->hasPermission($permission)) {
            abort(403, "You do not have the required '{$permission}' permission to access this resource.");
        }

        return $next($request);
    }
}
