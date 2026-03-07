<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveRequestStoreRequest;
use App\Models\Doctor;
use App\Models\LeaveRequest;
use App\Models\Nurse;
use App\Models\StaffLeaveRequest;
use Illuminate\Http\Request;

class StaffLeaveController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::with('doctor')->get();
        return view('staff.leave', compact('leaveRequests'));
    }
    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->update(['status' => 'approved']);

        return back()->with('ok', 'Approved');
    }

    public function reject($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->update(['status' => 'rejected']);
        return back()->with('ok', 'Rejected');
    }
    public function create()
    {
        $doctors = Doctor::all();
        $nurses = Nurse::all();
        $leaveRequests = LeaveRequest::all();
        return view('staff.leave_create',compact('doctors','nurses','leaveRequests'));
    }
    public function store(LeaveRequestStoreRequest $request)
    {

        $data = $request->validated();

        $leave = StaffLeaveRequest::create($data);

        return back()->with('success','Leave request submitted successfully');
    }
}
