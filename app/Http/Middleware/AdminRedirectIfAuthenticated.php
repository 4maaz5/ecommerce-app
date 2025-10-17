<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Check if the user is already logged in as admin
        if (Auth::guard('admin')->check()) {
            // If yes, redirect to the admin dashboard
            return redirect()->route('admin.dashboard');
        }

        // Otherwise, allow the request to proceed
        return $next($request);
    }
}
