<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffMedicalPositionController extends Controller
{
    public function index(): View
    {
        $applications = JobApplication::query()
            ->with('job:id,title')
            ->latest()
            ->get();

        return view('staff.job_applications', compact('applications'));
    }

    public function approve(int $id): RedirectResponse
    {
        JobApplication::findOrFail($id)->update([
            'status' => 'approved',
        ]);

        return back();
    }

    public function reject(int $id): RedirectResponse
    {
        JobApplication::findOrFail($id)->update([
            'status' => 'rejected',
        ]);

        return back();
    }

    public function administrative(): RedirectResponse
    {
        return redirect()->route('staff.dashboard');
    }
}
