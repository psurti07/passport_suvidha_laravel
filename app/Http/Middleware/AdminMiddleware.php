<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow access if user is logged in AND (is Admin OR is Staff)
        if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isStaff())) {
            return $next($request);
        }

        // If not logged in or not Admin/Staff, log out and redirect to login
        Auth::logout();
        return redirect()->route('login')->with('error', 'Unauthorized access. Admin or Staff privileges required.');
    }
} 