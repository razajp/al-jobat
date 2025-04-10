<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSession;

class CheckActiveSession
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            // Get the current user's session token (Laravel session ID)
            $sessionToken = session()->getId();

            // Check if the session is active within the last 60 minutes
            $userSession = UserSession::where('user_id', Auth::id())
                ->where('session_token', $sessionToken)
                ->where('is_active', true)
                ->where('last_activity', '>=', now()->subMinutes(60))
                ->first();

            // If no active session is found, log the user out
            if (!$userSession) {
                Auth::logout();
                return redirect(route('login'))->with('error', 'Session has expired or is not active.');
            }
        }

        return $next($request);
    }
}
