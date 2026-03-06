<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobApplicationRequest;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function create($id)
    {
        $job = Job::findOrFail($id);
        return view('jobs.apply', compact('job'));
    }

    public function store(JobApplicationRequest $request)
    {
//        dd('store reached');
        $data = $request->validated();

        $cvPath = $request->file('cv')->store('cvs', 'public');
        $data['cv'] = $cvPath;
        JobApplication::create($data);

        return redirect()->back()->with('success', 'Application submitted successfully!');
    }
}
