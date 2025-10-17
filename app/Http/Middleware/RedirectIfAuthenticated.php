<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // If no specific guard is provided, default to the web guard
        $guards = empty($guards) ? [null] : $guards;

        // Loop through the guards to check if the user is authenticated
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If authenticated, redirect to user profile page
                return redirect()->route('account.profile');
            }
        }

        // If not authenticated, allow the request to proceed
        return $next($request);
    }
}
