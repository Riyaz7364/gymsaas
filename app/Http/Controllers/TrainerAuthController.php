<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TrainerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('trainer.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::guard('trainer')->attempt($credentials, $request->boolean('remember'))) {
            return redirect()->route('trainer.dashboard');
        }

        return back()->withErrors(['username' => 'Invalid trainer credentials.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('trainer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('trainer.login');
    }

    public function dashboard()
    {
        $trainer = Auth::guard('trainer')->user();
        $trainer->load([
            'members.activePlan.plan',
            'schedules.members',
            'workoutPlans.member',
            'reviews.member',
            'memberHistories',
        ]);

        $stats = [
            'assigned_members' => $trainer->members->count(),
            'workout_plans' => $trainer->workoutPlans->count(),
            'reviews' => $trainer->reviews->count(),
            'renewals' => $trainer->memberHistories->where('action', 'renewed')->count(),
            'left_members' => $trainer->memberHistories->where('action', 'left')->count(),
        ];

        return view('trainer.dashboard', compact('trainer', 'stats'));
    }
}
