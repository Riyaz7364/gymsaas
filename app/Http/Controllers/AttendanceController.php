<?php

namespace App\Http\Controllers;

use App\Jobs\SendAttendanceAiMessage;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\MemberWorkoutProgress;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $gymId = auth()->user()->gym_id;
        $date  = $request->get('date', today()->toDateString());

        $records = Attendance::with('member')
            ->where('gym_id', $gymId)
            ->whereDate('check_in', $date)
            ->orderByDesc('check_in')
            ->paginate(30);

        $todayCount = Attendance::where('gym_id', $gymId)
            ->whereDate('check_in', today())
            ->count();

        $monthCount = Attendance::where('gym_id', $gymId)
            ->whereMonth('check_in', now()->month)
            ->whereYear('check_in', now()->year)
            ->count();

        // Still inside (no checkout)
        $insideCount = Attendance::where('gym_id', $gymId)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->count();

        return view('attendance.index', compact('records', 'date', 'todayCount', 'monthCount', 'insideCount'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'member_id' => ['required', 'integer'],
        ]);

        $gymId  = auth()->user()->gym_id;
        $member = Member::where('gym_id', $gymId)->findOrFail($request->member_id);

        // Already checked in today without checkout
        $open = Attendance::where('gym_id', $gymId)
            ->where('member_id', $member->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        if ($open) {
            return back()->with('error', "{$member->name} is already checked in (no check-out yet).");
        }

        Attendance::create([
            'gym_id'    => $gymId,
            'member_id' => $member->id,
            'check_in'  => now(),
            'method'    => 'manual',
        ]);

        // Advance the member's workout sequence step
        MemberWorkoutProgress::forMember($member->id, $gymId)->advance();

        // Dispatch AI WhatsApp message if member has opted in
        if ($member->whatsapp_optin) {
            $att = Attendance::where('gym_id', $gymId)
                ->where('member_id', $member->id)
                ->latest()
                ->first();
            SendAttendanceAiMessage::dispatch($att->id, $member->id)->delay(now()->addSeconds(5));
        }

        return back()->with('success', "{$member->name} checked in successfully.");
    }

    public function checkOut(Request $request, Attendance $attendance)
    {
        abort_if($attendance->gym_id !== auth()->user()->gym_id, 403);
        abort_if($attendance->check_out !== null, 422, 'Already checked out.');

        $attendance->update(['check_out' => now()]);

        return back()->with('success', "Check-out recorded for {$attendance->member->name}.");
    }

    /**
     * AJAX member search for check-in form
     */
    public function searchMembers(Request $request)
    {
        $q     = $request->get('q', '');
        $gymId = auth()->user()->gym_id;

        $members = Member::where('gym_id', $gymId)
            ->where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('member_no', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'member_no', 'phone', 'avatar')
            ->limit(10)
            ->get()
            ->map(fn($m) => [
                'id'        => $m->id,
                'name'      => $m->name,
                'member_no' => $m->member_no,
                'phone'     => $m->phone,
                'avatar'    => $m->avatar
                    ? asset('storage/'.$m->avatar)
                    : 'https://ui-avatars.com/api/?name='.urlencode($m->name).'&color=fff&background=0abf8e&size=32&bold=true',
            ]);

        return response()->json($members);
    }

    /**
     * @deprecated Use resource routes only; kept to avoid abstract-method errors
     */
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function show(string $id) { abort(404); }
    public function edit(string $id) { abort(404); }
    public function update(Request $request, string $id) { abort(404); }
    public function destroy(string $id) { abort(404); }
}
