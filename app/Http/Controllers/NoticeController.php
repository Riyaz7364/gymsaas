<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $gymId   = auth()->user()->gym_id;
        $notices = Notice::where('gym_id', $gymId)->with('createdBy')->latest()->paginate(15);
        return view('notices.index', compact('notices'));
    }

    public function create()
    {
        return view('notices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'body'         => 'required|string',
            'audience'     => 'required|in:all,trainers,members,staff',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date',
        ]);
        $data['gym_id']     = auth()->user()->gym_id;
        $data['created_by'] = auth()->id();
        Notice::create($data);
        return redirect()->route('notices.index')->with('success', 'Notice published.');
    }

    public function show(string $id)
    {
        $gymId  = auth()->user()->gym_id;
        $notice = Notice::where('gym_id', $gymId)->findOrFail($id);
        return view('notices.show', compact('notice'));
    }

    public function edit(string $id)
    {
        $gymId  = auth()->user()->gym_id;
        $notice = Notice::where('gym_id', $gymId)->findOrFail($id);
        return view('notices.edit', compact('notice'));
    }

    public function update(Request $request, string $id)
    {
        $gymId  = auth()->user()->gym_id;
        $notice = Notice::where('gym_id', $gymId)->findOrFail($id);
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'body'         => 'required|string',
            'audience'     => 'required|in:all,trainers,members,staff',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date',
        ]);
        $notice->update($data);
        return redirect()->route('notices.index')->with('success', 'Notice updated.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        Notice::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect()->route('notices.index')->with('success', 'Notice deleted.');
    }
}
