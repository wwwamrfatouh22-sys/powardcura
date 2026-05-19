<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function create(): View
    {
        return view('complaints.create');
    }

    public function store(ComplaintRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = 'pending';
        $data['priority'] = 'medium';

        Complaint::create($data);

        return redirect('/')->with('success', 'Your complaint has been submitted successfully.');
    }
}
