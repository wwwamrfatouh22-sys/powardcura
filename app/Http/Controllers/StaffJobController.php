<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffJobStoreRequest;
use App\Models\Job;
use App\Support\TableFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffJobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = Job::query()
            ->latest();

        TableFilters::apply($jobs, $request, [
            'date_column' => 'created_at',
            'type_column' => 'type',
            'status_column' => 'status',
        ]);

        $jobs = $jobs->paginate(10)->appends($request->query());

        return view('staff.jobs', compact('jobs'));
    }

    public function store(StaffJobStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['location'] = $data['location'] ?? 'NUH';

        Job::create($data);

        return redirect()
            ->route('staff.jobs.index')
            ->with('success', 'Job published successfully.');
    }
}
