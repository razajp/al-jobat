<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        // Check if the user is authenticated and has one of the allowed roles
        if (!in_array(auth()->user()->role, $roles)) {
            // If not, redirect or abort
            return redirect('/home');  // Or abort(403) to send a forbidden response
        }

        // Allow the request to proceed
        return $next($request);
    }
}
