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
            && $user->role === 'trainer'
            && $user->must_change_password
            && ! $request->routeIs('trainer.password.change', 'trainer.password.update', 'logout')
        ) {
            return redirect()->route('trainer.password.change');
        }

        return $next($request);
    }
}
