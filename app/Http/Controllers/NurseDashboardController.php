<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class NurseDashboardController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor'])->get();
        return view('nurse.dashboard', compact('appointments'));
    }
}
