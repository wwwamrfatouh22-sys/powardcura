<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function create()
    {
        return view('complaints.create');
    }

    public function store(ComplaintRequest $request)
    {
        $data = $request->validated();

        Complaint::create($data);

        return redirect('/')->with('success', 'Your complaint has been submitted successfully.');
    }
}
