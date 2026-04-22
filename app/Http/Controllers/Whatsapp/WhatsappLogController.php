<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsappLogController extends Controller
{
    public function index() { return view('whatsapp.logs'); }
}
