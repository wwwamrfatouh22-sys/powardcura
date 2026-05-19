<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobApplicationRequest;
use App\Models\Job;
use App\Models\JobApplication;
use App\Support\AuditLogger;
use App\Support\ProtectedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    public function create(Job $job): View
    {
        return view('jobs.apply', compact('job'));
    }

    public function store(JobApplicationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        abort_unless(Job::query()->whereKey($data['job_id'])->where('status', 'active')->exists(), 404);

        $cvPath = ProtectedFile::storeJobCv($request->file('cv'));

        $application = JobApplication::create([
            'job_id' => $data['job_id'],
            'name' => $data['full_name'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'national_id' => $data['national_id'],
            'cv' => $cvPath,
            'cv_path' => $cvPath,
            'status' => 'pending',
        ]);
        AuditLogger::log('job_application.created', $application);

        return redirect()
            ->route('jobs.show', $data['job_id'])
            ->with('success', 'Your job application has been submitted successfully.');
    }
}
