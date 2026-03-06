<?php

namespace App\Http\Controllers;

use App\Models\MedicalPosition;
use Illuminate\Http\Request;

class StaffMedicalPositionController extends Controller
{
    public function index()
    {
        $positions = MedicalPosition::with('department')->get();

        return view('staff.medical_positions', compact('positions'));
    }
    public function approve($id)
    {
        $position = MedicalPosition::findOrFail($id);

        $position->update([
            'status' => 'approved'
        ]);

        return back();
    }

    public function reject($id)
    {
        $position = MedicalPosition::findOrFail($id);

        $position->update([
            'status' => 'rejected'
        ]);

        return back();
    }
    public function administrative()
    {
        $positions = MedicalPosition::with('department')->get();

        return view('staff.administrative_positions', compact('positions'));
    }
}
