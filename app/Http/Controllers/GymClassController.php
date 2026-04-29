<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\Trainer;
use Illuminate\Http\Request;

class GymClassController extends Controller
{
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $classes = GymClass::where('gym_id', $gymId)
            ->with('trainer')
            ->latest()
            ->paginate(15);
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $gymId   = auth()->user()->gym_id;
        $trainers = Trainer::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get();
        return view('classes.create', compact('trainers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'description'   => 'nullable|string',
            'trainer_id'    => 'nullable|exists:trainers,id',
            'schedule_days' => 'required|array|min:1',
            'schedule_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'capacity'      => 'required|integer|min:1',
            'room'          => 'nullable|string|max:100',
            'status'        => 'required|in:active,inactive',
        ]);
        $data['gym_id'] = auth()->user()->gym_id;
        GymClass::create($data);
        return redirect(gym_route('gym.classes.index'))->with('success', 'Class created successfully.');
    }

    public function show(string $id)
    {
        $gymId = auth()->user()->gym_id;
        $class = GymClass::where('gym_id', $gymId)->with('trainer')->findOrFail($id);
        return view('classes.show', compact('class'));
    }

    public function edit(string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $class   = GymClass::where('gym_id', $gymId)->findOrFail($id);
        $trainers = Trainer::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get();
        return view('classes.edit', compact('class', 'trainers'));
    }

    public function update(Request $request, string $id)
    {
        $gymId = auth()->user()->gym_id;
        $class = GymClass::where('gym_id', $gymId)->findOrFail($id);
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'description'   => 'nullable|string',
            'trainer_id'    => 'nullable|exists:trainers,id',
            'schedule_days' => 'required|array|min:1',
            'schedule_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'capacity'      => 'required|integer|min:1',
            'room'          => 'nullable|string|max:100',
            'status'        => 'required|in:active,inactive',
        ]);
        $class->update($data);
        return redirect(gym_route('gym.classes.index'))->with('success', 'Class updated successfully.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        GymClass::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect(gym_route('gym.classes.index'))->with('success', 'Class deleted.');
    }
}
