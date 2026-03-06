<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function medical()
    {
        $jobs = Job::where('type', 'medical')->latest()->get();

        return view('jobs.medical', compact('jobs'));
    }
}
