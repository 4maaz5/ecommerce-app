<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class AdminAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // If the request expects JSON, do not redirect
        // Otherwise, redirect to the admin login page
        return $request->expectsJson() ? null : route('admin.login');
    }

    /**
     * Authenticate the user for the 'admin' guard.
     */
    protected function authenticate($request, array $guards)
    {
        // Check if the user is logged in using the 'admin' guard
        if ($this->auth->guard('admin')->check()) {
            // If logged in, use the 'admin' guard for authentication
            return $this->auth->shouldUse('admin');
        }

        // If not logged in, handle unauthenticated access
        $this->unauthenticated($request, ['admin']);
    }
}
