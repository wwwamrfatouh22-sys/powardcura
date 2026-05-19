<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\RadiologyResult;
use Illuminate\View\View;

class AdminMedicalResultController extends Controller
{
    public function radiology(): View
    {
        return view('admin.medical_results', [
            'title' => 'Radiology',
            'type' => 'radio',
            'results' => RadiologyResult::query()->with('patient')->latest()->paginate(12),
        ]);
    }

    public function laboratory(): View
    {
        return view('admin.medical_results', [
            'title' => 'Laboratory',
            'type' => 'lab',
            'results' => LabTest::query()->with('patient')->latest()->paginate(12),
        ]);
    }
}
