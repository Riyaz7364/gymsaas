<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $gymId  = auth()->user()->gym_id;
        $events = Event::where('gym_id', $gymId)->with('eventType')->latest('start_datetime')->paginate(15);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        $gymId      = auth()->user()->gym_id;
        $eventTypes = EventType::where('gym_id', $gymId)->orderBy('name')->get();
        return view('events.create', compact('eventTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:200',
            'description'    => 'nullable|string',
            'event_type_id'  => 'nullable|exists:event_types,id',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'nullable|date|after_or_equal:start_datetime',
            'all_day'        => 'boolean',
            'color'          => 'nullable|string|max:7',
            'location'       => 'nullable|string|max:200',
        ]);
        $data['gym_id']     = auth()->user()->gym_id;
        $data['created_by'] = auth()->id();
        $data['all_day']    = $request->boolean('all_day');
        Event::create($data);
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(string $id)
    {
        $gymId = auth()->user()->gym_id;
        $event = Event::where('gym_id', $gymId)->with('eventType')->findOrFail($id);
        return view('events.show', compact('event'));
    }

    public function edit(string $id)
    {
        $gymId      = auth()->user()->gym_id;
        $event      = Event::where('gym_id', $gymId)->findOrFail($id);
        $eventTypes = EventType::where('gym_id', $gymId)->orderBy('name')->get();
        return view('events.edit', compact('event', 'eventTypes'));
    }

    public function update(Request $request, string $id)
    {
        $gymId = auth()->user()->gym_id;
        $event = Event::where('gym_id', $gymId)->findOrFail($id);
        $data = $request->validate([
            'title'          => 'required|string|max:200',
            'description'    => 'nullable|string',
            'event_type_id'  => 'nullable|exists:event_types,id',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'nullable|date|after_or_equal:start_datetime',
            'all_day'        => 'boolean',
            'color'          => 'nullable|string|max:7',
            'location'       => 'nullable|string|max:200',
        ]);
        $data['all_day'] = $request->boolean('all_day');
        $event->update($data);
        return redirect()->route('events.index')->with('success', 'Event updated.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        Event::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }

    public function calendarData(Request $request)
    {
        $gymId  = auth()->user()->gym_id;
        $events = Event::where('gym_id', $gymId)
            ->select('id', 'title', 'start_datetime', 'end_datetime', 'all_day', 'color')
            ->get()
            ->map(fn($e) => [
                'id'    => $e->id,
                'title' => $e->title,
                'start' => $e->start_datetime,
                'end'   => $e->end_datetime,
                'allDay'=> $e->all_day,
                'color' => $e->color ?? '#0abf8e',
            ]);
        return response()->json($events);
    }
}
