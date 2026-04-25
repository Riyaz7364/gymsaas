<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;

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
}
