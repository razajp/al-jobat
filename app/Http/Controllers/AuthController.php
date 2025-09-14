<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->back()->with('error', 'Oops! You’re already loged in. Please logout first!');
        }
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        if (Auth::check()) {
            return redirect(route('home'))->with('warning', 'Oops! You’re already loged in. Please logout first!');
        } else {
            // Validate the input
            $request->validate([
                'username' => 'required|string', // Username field validation
                'password' => 'required',        // Password field validation
            ]);

            // Attempt to find the user by username
            $user = User::where('username', $request->username)->first();

            // Check if user exists and if the password matches the hash
            if ($user && Hash::check($request->password, $user->password)) {

                $existingSession = UserSession::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->where('last_activity', '>=', now()->subMinutes(60)) // optional timeout
                    ->first();

                if ($existingSession) {
                    // return back with error message
                    return redirect('/login')->with('error', 'Already logged in on another device.');
                }

                if ($user->status == 'active') {
                    // Password is correct, login the user
                    Auth::login($user);

                    UserSession::create([
                        'user_id' => $user->id,
                        'session_token' => session()->getId(),
                        'last_activity' => now(),
                    ]);

                    return redirect(route('home'))->with('success', 'Welcome back! You have successfully logged in.'); // Redirect to home
                } else {
                    return redirect('/login')->with('error', 'Your account is currently inactive. Please reach out to the administrator for assistance.')
                        ->withInput($request->only('username'));
                }
            }

            // If authentication fails, return error message but do not keep the password in session
            if (!$user) {
                return redirect('/login')->withErrors(['username' => 'Invalid username'])
                    ->withInput($request->only('username'));
            } else {
                return redirect('/login')->withErrors(['password' => 'Invalid password'])
                    ->withInput($request->only('username'));
            }
        }
    }

    // Normal logout
    public function logout(Request $request)
    {
        if (auth()->check()) {
            UserSession::where('user_id', auth()->id())
                ->where('session_token', session()->getId())
                ->update([
                    'is_active' => false,
                    'last_activity' => now(),
                ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Logout successful! You’ve been logged out. See you soon!');
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark', // Validate theme input
        ]);

        $user = Auth::user();
        User::where('id', $user->id)->update(['theme' => $request->theme]);

        return response()->json([
            'success' => true,
            'message' => 'Theme updated successfully! Your preferences have been saved.'
        ]);
    }

    public function updateLastActivity(Request $request)
    {
        if (auth()->check()) {
            UserSession::where('user_id', auth()->id())
                ->where('session_token', session()->getId())
                ->update(['last_activity' => now()]);
            return response()->json(['status' => 'updated']);
        }

        return response()->json(['status' => 'unauthorized'], 401);
    }

    public function updateMenuShortcuts(Request $request)
    {
        if (auth()->check()) {
            $userId = Auth::user()->id;
            User::where('id', $userId)->update(['menu_shortcuts' => $request->menu_shortcuts]);

            return response()->json(['status' => 'updated']);
        }

        return response()->json(['status' => 'unauthorized'], 401);
    }
}
