<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function members()  { return view('reports.members'); }
    public function revenue()  { return view('reports.revenue'); }
    public function trainers() { return view('reports.trainers'); }
}
