<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class StaffComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::all();

        return view('staff.complaints',compact('complaints'));
    }
    public function resolve($id)
    {

        Complaint::findOrFail($id)->update([
            'status'=>'resolved'
        ]);

        return back();
    }

    public function escalate($id)
    {

        Complaint::findOrFail($id)->update([
            'priority'=>'high'
        ]);

        return back();
    }
}
