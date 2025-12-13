<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSession;

class SubscriptionExpiry
{
    public function handle($request, Closure $next)
    {
        // Subscription mode & expiry date
        $mode = config('app.subscription_mode', 'demo'); // demo or paid
        $expiryDate = config('app.subscription_expire');

        // No expiry date → skip
        if (!$expiryDate) {
            return $next($request);
        }

        $expiry = Carbon::parse($expiryDate)->startOfDay();
        $today = Carbon::now()->startOfDay();

        // Expired subscription → logout + show expired page
        if ($today->greaterThan($expiry)) {

            if (Auth::check()) {
                $userId = Auth::id();

                // Mark DB sessions inactive
                UserSession::where('user_id', $userId)
                    ->where('is_active', true)
                    ->update(['is_active' => false, 'last_activity' => now()]);

                Auth::logout();
            }

            // Clear old warnings
            session()->forget('expiry_warning_shown');
            session()->forget('warning');

            // Redirect to dedicated route
            return redirect()->route('subscription-expired');
        }

        // Show warning 3 days before expiry (once per day)
        $daysLeft = $today->diffInDays($expiry, false);

        if ($daysLeft <= 3 && $daysLeft >= 0) {
            $lastShown = session('expiry_warning_shown');

            if ($lastShown !== $today->toDateString()) {
                // Flash warning in a standard key
                session()->flash('warning', "Your $mode subscription will expire in $daysLeft day(s). Expiry Date: " . $expiry->format('d-M-Y, D'));
                session(['expiry_warning_shown' => $today->toDateString()]);
            }
        }

        return $next($request);
    }
}
