<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappCampaign;
use Illuminate\Http\Request;

class WhatsappCampaignController extends Controller
{
    public function index()
    {
        $gymId     = auth()->user()->gym_id;
        $campaigns = WhatsappCampaign::where('gym_id', $gymId)
            ->latest()
            ->paginate(20);
        return view('whatsapp.campaigns.index', compact('campaigns'));
    }

    public function create() { return view('whatsapp.campaigns.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:150',
            'body'         => 'required|string',
            'scheduled_at' => 'nullable|date',
        ]);
        $data['gym_id']     = auth()->user()->gym_id;
        $data['created_by'] = auth()->id();
        $data['status']     = 'draft';
        WhatsappCampaign::create($data);
        return redirect()->route('whatsapp.campaigns.index')->with('success', 'Campaign saved as draft.');
    }

    public function edit(WhatsappCampaign $campaign)
    {
        return redirect()->route('whatsapp.campaigns.index')
            ->with('info', 'Editing campaigns coming soon.');
    }

    public function update(Request $request, WhatsappCampaign $campaign)
    {
        return redirect()->route('whatsapp.campaigns.index');
    }

    public function destroy(WhatsappCampaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('whatsapp.campaigns.index')->with('success', 'Campaign deleted.');
    }
}
