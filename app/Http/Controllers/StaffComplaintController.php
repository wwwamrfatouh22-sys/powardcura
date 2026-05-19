<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Support\TableFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $complaints = Complaint::query()
            ->latest();

        TableFilters::apply($complaints, $request, [
            'date_column' => 'created_at',
            'status_column' => 'status',
        ]);

        $complaints = $complaints->paginate(10)->appends($request->query());

        return view('staff.complaints_index', [
            'complaints' => $complaints ?? collect(),
        ]);
    }

    public function resolve(int $id): RedirectResponse
    {
        Complaint::findOrFail($id)->update([
            'status' => 'resolved',
        ]);

        return back()->with('success', 'Complaint marked as resolved.');
    }

    public function escalate(int $id): RedirectResponse
    {
        Complaint::findOrFail($id)->update([
            'priority' => 'high',
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Complaint escalated successfully.');
    }
}
