<?php

namespace App\Http\Controllers;

use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::latest()->get();

        return view('nurse.patients', compact('patients'));
    }
}
