<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SignupSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(private SignupSettings $signupSettings) {}

    public function index()
    {
        $settings = $this->signupSettings->all();
        return view('super-admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'trial_enabled'       => ['nullable', 'boolean'],
            'trial_days'          => ['required', 'integer', 'min:1', 'max:365'],
            'trial_badge'         => ['required', 'string', 'max:40'],
            'subscribe_enabled'   => ['nullable', 'boolean'],
            'annual_discount_pct' => ['required', 'integer', 'min:0', 'max:80'],
            'trial_card_title'    => ['required', 'string', 'max:60'],
            'trial_card_desc'     => ['required', 'string', 'max:120'],
            'sub_card_title'      => ['required', 'string', 'max:60'],
        ]);

        // Checkboxes submit nothing when unchecked — normalise to bool
        $data['trial_enabled']     = $request->boolean('trial_enabled');
        $data['subscribe_enabled'] = $request->boolean('subscribe_enabled');

        $this->signupSettings->save($data);

        return back()->with('success', 'Signup settings saved successfully.');
    }
}
