<?php

namespace App\Http\Controllers;

use App\Models\ContactDiary;
use Illuminate\Http\Request;

class ContactDiaryController extends Controller
{
    public function index()
    {
        $gymId   = auth()->user()->gym_id;
        $contacts = ContactDiary::where('gym_id', $gymId)->latest()->paginate(20);
        return view('contact-diary.index', compact('contacts'));
    }

    public function create()
    {
        return view('contact-diary.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_name'   => 'required|string|max:150',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'type'           => 'nullable|string|max:50',
            'notes'          => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status'         => 'required|in:open,done,cancelled',
        ]);
        $data['gym_id']     = auth()->user()->gym_id;
        $data['assigned_to'] = auth()->id();
        ContactDiary::create($data);
        return redirect()->route('contact-diary.index')->with('success', 'Contact added.');
    }

    public function show(string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $contact = ContactDiary::where('gym_id', $gymId)->findOrFail($id);
        return view('contact-diary.show', compact('contact'));
    }

    public function edit(string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $contact = ContactDiary::where('gym_id', $gymId)->findOrFail($id);
        return view('contact-diary.edit', compact('contact'));
    }

    public function update(Request $request, string $id)
    {
        $gymId   = auth()->user()->gym_id;
        $contact = ContactDiary::where('gym_id', $gymId)->findOrFail($id);
        $data = $request->validate([
            'contact_name'   => 'required|string|max:150',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'type'           => 'nullable|string|max:50',
            'notes'          => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status'         => 'required|in:open,done,cancelled',
        ]);
        $contact->update($data);
        return redirect()->route('contact-diary.index')->with('success', 'Contact updated.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        ContactDiary::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect()->route('contact-diary.index')->with('success', 'Contact deleted.');
    }
}