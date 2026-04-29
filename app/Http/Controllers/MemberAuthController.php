<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\TrainerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('member.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $member = Member::where('phone', $request->phone)->first();

        if ($member && \Hash::check($request->password, $member->password)) {
            Auth::guard('member')->login($member);
            return redirect()->route('member.dashboard');
        }

        return back()->withErrors(['phone' => 'Invalid credentials']);
    }

    public function scanQr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $member = Auth::guard('member')->user();

        // Parse QR data - assume format: gym_id-date-token
        $parts = explode('-', $request->qr_data);
        if (count($parts) !== 3) {
            return response()->json(['message' => 'Invalid QR code'], 400);
        }

        [$gymId, $date, $token] = $parts;

        if ($gymId != $member->gym_id || $date !== today()->format('Y-m-d')) {
            return response()->json(['message' => 'Invalid QR code for today'], 400);
        }

        // Check token
        $storedToken = \Cache::get("qr_token_{$gymId}_{$date}");
        if (!$storedToken || $storedToken !== $token) {
            return response()->json(['message' => 'Invalid QR code'], 400);
        }

        // Check if already checked in today
        $existing = \App\Models\Attendance::where('member_id', $member->id)
            ->whereDate('check_in', today())
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already checked in today'], 400);
        }

        // Create attendance
        \App\Models\Attendance::create([
            'gym_id' => $member->gym_id,
            'member_id' => $member->id,
            'check_in' => now(),
        ]);

        return response()->json(['message' => 'Attendance marked successfully']);
    }

    public function dashboard()
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return redirect()->route('member.login');
        }

        $member->load([
            'attendances' => fn($q) => $q->latest()->limit(10),
            'workoutPlan.items.activity',
            'dietPlan.meals',
            'activePlan.plan',
        ]);

        return view('member.dashboard', compact('member'));
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login');
    }

    public function submitTrainerReview(Request $request)
    {
        $member = Auth::guard('member')->user();
        if (! $member) {
            return redirect()->route('member.login');
        }

        $trainer = $member->trainer->first();
        if (! $trainer) {
            return back()->withErrors(['trainer' => 'No trainer assigned to submit a review.']);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        TrainerReview::create([
            'gym_id' => $member->gym_id,
            'trainer_id' => $trainer->id,
            'member_id' => $member->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Thank you for sharing your feedback.');
    }
}
