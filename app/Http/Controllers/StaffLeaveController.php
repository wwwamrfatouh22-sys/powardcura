<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveRequestStoreRequest;
use App\Models\LeaveRequest;
use App\Models\StaffLeaveRequest;
use Illuminate\Http\RedirectResponse;

class StaffLeaveController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('staff.dashboard');
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
    public function create(): RedirectResponse
    {
        return redirect()->route('staff.dashboard');
    }
    public function store(LeaveRequestStoreRequest $request)
    {

        $data = $request->validated();

        $leave = StaffLeaveRequest::create($data);

        return back()->with('success','Leave request submitted successfully');
    }
}
