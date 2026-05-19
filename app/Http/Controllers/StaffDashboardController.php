<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\TrainingRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (in_array(Auth::guard('staff')->user()?->role, ['radiology_lab', 'radiology', 'laboratory', 'lab'], true)) {
            return redirect()->route('staff.radiology_lab');
        }

        $jobApplicationsCount = JobApplication::count();
        $trainingRegistrationsCount = TrainingRegistration::count();
        $complaintsCount = Complaint::count();
        $publishedJobsCount = Job::count();

        return view('staff.dashboard', compact('jobApplicationsCount', 'trainingRegistrationsCount', 'complaintsCount', 'publishedJobsCount'));
    }
}
