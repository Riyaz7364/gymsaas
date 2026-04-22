<?php

namespace App\Http\Controllers\Health;

use App\Http\Controllers\Controller;
use App\Models\BodyStat;
use App\Models\Member;
use Illuminate\Http\Request;

class BodyStatController extends Controller
{
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $stats = BodyStat::where('gym_id', $gymId)
            ->with('member')
            ->orderByDesc('date')
            ->paginate(20);
        return view('body-stats.index', compact('stats'));
    }

    public function create()
    {
        $gymId   = auth()->user()->gym_id;
        $members = Member::where('gym_id', $gymId)->orderBy('name')->get();
        return view('body-stats.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'    => 'required|exists:members,id',
            'date'         => 'required|date',
            'weight'       => 'nullable|numeric|min:0',
            'height'       => 'nullable|numeric|min:50|max:250',
            'body_fat_pct' => 'nullable|numeric|min:0|max:100',
            'waist'        => 'nullable|numeric|min:0',
            'chest'        => 'nullable|numeric|min:0',
            'arms'         => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);
        $data['gym_id'] = auth()->user()->gym_id;
        if (!empty($data['weight']) && !empty($data['height'])) {
            $h = $data['height'] / 100;
            $data['bmi'] = round($data['weight'] / ($h * $h), 1);
        }
        BodyStat::create($data);
        return redirect()->route('body-stats.index')->with('success', 'Body stats recorded.');
    }

    public function edit(BodyStat $bodyStat)
    {
        $gymId   = auth()->user()->gym_id;
        $members = Member::where('gym_id', $gymId)->orderBy('name')->get();
        return view('body-stats.edit', ['stat' => $bodyStat, 'members' => $members]);
    }

    public function update(Request $request, BodyStat $bodyStat)
    {
        $data = $request->validate([
            'member_id'    => 'required|exists:members,id',
            'date'         => 'required|date',
            'weight'       => 'nullable|numeric|min:0',
            'height'       => 'nullable|numeric|min:50|max:250',
            'body_fat_pct' => 'nullable|numeric|min:0|max:100',
            'waist'        => 'nullable|numeric|min:0',
            'chest'        => 'nullable|numeric|min:0',
            'arms'         => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);
        if (!empty($data['weight']) && !empty($data['height'])) {
            $h = $data['height'] / 100;
            $data['bmi'] = round($data['weight'] / ($h * $h), 1);
        }
        $bodyStat->update($data);
        return redirect()->route('body-stats.index')->with('success', 'Body stats updated.');
    }

    public function destroy(BodyStat $bodyStat)
    {
        $bodyStat->delete();
        return redirect()->route('body-stats.index')->with('success', 'Body stats deleted.');
    }
}
