<?php

namespace App\Http\Controllers;

use App\Models\TrainingProgram;
use Illuminate\Http\Request;

class StaffTrainingProgramController extends Controller
{
    public function index()
    {
        $programs = TrainingProgram::with('department')->get();

        return view('staff.training_programs',compact('programs'));
    }

    public function approve($id)
    {
        TrainingProgram::findOrFail($id)->update([
            'status'=>'approved'
        ]);

        return back();
    }

    public function reject($id)
    {
        TrainingProgram::findOrFail($id)->update([
            'status'=>'rejected'
        ]);

        return back();
    }

}
