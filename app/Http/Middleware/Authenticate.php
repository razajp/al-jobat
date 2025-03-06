<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            Session::flash('error', 'Security Alert! Unauthorized attempt detected! Please log in.');
            return route('login');
        }

        abort(response()->json(['message' => 'Unauthorized'], 401));
    }
}
