<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
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
}
