<?php

namespace App\Http\Controllers;

use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['patient', 'doctor', 'department'])
            ->latest()
            ->get();

        return view('nurse.reports', compact('reports'));
    }
    public function toggleReviewed(Report $report)
    {
        $report->update([
            'is_reviewed' => !$report->is_reviewed,
            'status' => !$report->is_reviewed ? 'Completed' : 'Pending'
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}
