<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()          { return view('settings.index'); }
    public function updateProfile(\Illuminate\Http\Request $r)  { return back()->with('success', 'Profile updated.'); }
    public function updatePassword(\Illuminate\Http\Request $r) { return back()->with('success', 'Password updated.'); }
    public function updateGeneral(\Illuminate\Http\Request $r)  { return back()->with('success', 'Settings saved.'); }
    public function updateCompany(\Illuminate\Http\Request $r)  { return back()->with('success', 'Company info updated.'); }
    public function updateEmail(\Illuminate\Http\Request $r)    { return back()->with('success', 'Email settings saved.'); }
    public function updatePayment(\Illuminate\Http\Request $r)  { return back()->with('success', 'Payment settings saved.'); }
    public function updateWhatsapp(\Illuminate\Http\Request $r) { return back()->with('success', 'WhatsApp settings saved.'); }
    public function updateOpenai(\Illuminate\Http\Request $r)   { return back()->with('success', 'OpenAI settings saved.'); }
}
