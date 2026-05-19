<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(): View
    {
        $jobs = Job::query()
            ->where('status', 'active')
            ->latest()
            ->get();

        $medicalJobs = $jobs->filter(fn (Job $job) => $job->type === 'medical')->values();
        $administrativeJobs = $jobs->filter(fn (Job $job) => in_array($job->type, ['administrative', 'admin'], true))->values();

        return view('jobs.medical', compact('medicalJobs', 'administrativeJobs'));
    }

    public function show(Job $job): View
    {
        return view('jobs.show', compact('job'));
    }

    public function medical(): View
    {
        return $this->index();
    }
}
