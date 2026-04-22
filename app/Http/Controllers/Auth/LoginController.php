<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            // Log failed attempt
            LoginHistory::create([
                'user_id'    => \App\Models\User::where('email', $request->email)->value('id'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'success'    => false,
                'logged_at'  => now(),
            ]);

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Log successful login
        LoginHistory::create([
            'user_id'    => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'success'    => true,
            'logged_at'  => now(),
        ]);

        // Update last login
        $user->updateQuietly([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return redirect($this->redirectPath($user));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectPath(\App\Models\User $user): string
    {
        if ($user->isSuperAdmin()) {
            return route('super-admin.dashboard');
        }
        return route('dashboard');
    }
}
