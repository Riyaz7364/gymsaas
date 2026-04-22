<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use App\Models\Member;
use Illuminate\Http\Request;

class LockerController extends Controller
{
    public function index()
    {
        $gymId   = auth()->user()->gym_id;
        $lockers = Locker::where('gym_id', $gymId)->with('member')->orderBy('locker_no')->paginate(20);
        $stats   = [
            'total'       => Locker::where('gym_id', $gymId)->count(),
            'available'   => Locker::where('gym_id', $gymId)->where('status', 'available')->count(),
            'occupied'    => Locker::where('gym_id', $gymId)->where('status', 'occupied')->count(),
            'maintenance' => Locker::where('gym_id', $gymId)->where('status', 'maintenance')->count(),
        ];
        return view('lockers.index', compact('lockers', 'stats'));
    }

    public function create()
    {
        $gymId   = auth()->user()->gym_id;
        $members = Member::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get();
        return view('lockers.create', compact('members'));
    }

    public function store(Request $request)
    {
        $gymId = auth()->user()->gym_id;
        $data = $request->validate([
            'locker_no'   => 'required|string|max:20',
            'member_id'   => 'nullable|exists:members,id',
            'status'      => 'required|in:available,occupied,maintenance',
            'notes'       => 'nullable|string',
            'assigned_at' => 'nullable|date',
            'expires_at'  => 'nullable|date',
        ]);
        $data['gym_id'] = $gymId;
        // Ensure unique locker_no per gym
        if (Locker::where('gym_id', $gymId)->where('locker_no', $data['locker_no'])->exists()) {
            return back()->withErrors(['locker_no' => 'Locker number already exists.'])->withInput();
        }
        Locker::create($data);
        return redirect()->route('lockers.index')->with('success', 'Locker created successfully.');
    }

    public function show(string $id)
    {
        $gymId  = auth()->user()->gym_id;
        $locker = Locker::where('gym_id', $gymId)->with('member')->findOrFail($id);
        return view('lockers.show', compact('locker'));
    }

    public function edit(string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $locker  = Locker::where('gym_id', $gymId)->findOrFail($id);
        $members = Member::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get();
        return view('lockers.edit', compact('locker', 'members'));
    }

    public function update(Request $request, string $id)
    {
        $gymId  = auth()->user()->gym_id;
        $locker = Locker::where('gym_id', $gymId)->findOrFail($id);
        $data = $request->validate([
            'locker_no'   => 'required|string|max:20',
            'member_id'   => 'nullable|exists:members,id',
            'status'      => 'required|in:available,occupied,maintenance',
            'notes'       => 'nullable|string',
            'assigned_at' => 'nullable|date',
            'expires_at'  => 'nullable|date',
        ]);
        if (Locker::where('gym_id', $gymId)->where('locker_no', $data['locker_no'])->where('id', '!=', $id)->exists()) {
            return back()->withErrors(['locker_no' => 'Locker number already exists.'])->withInput();
        }
        $locker->update($data);
        return redirect()->route('lockers.index')->with('success', 'Locker updated.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        Locker::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect()->route('lockers.index')->with('success', 'Locker deleted.');
    }
}
