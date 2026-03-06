<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
        public function show()
    {
        $patient = Patient::with('medications')->latest()->first();
        return view('profile.show', compact('patient'));
    }
}
