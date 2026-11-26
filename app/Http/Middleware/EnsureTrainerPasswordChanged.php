<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTrainerPasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user
            && $user->must_change_password
            && ! $request->routeIs('password.change', 'password.update', 'password.forced.update', 'trainer.password.change', 'trainer.password.update', 'logout')
        ) {
            $redirectRoute = $user->role === 'trainer' ? 'trainer.password.change' : 'password.change';

            return redirect()->route($redirectRoute);
        }

        return $next($request);
    }
}
