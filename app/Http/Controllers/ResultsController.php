<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\LabTest;
use App\Models\RadiologyResult;
use Illuminate\Http\Request;
use App\Models\Patient;

class ResultsController extends Controller
{
    public function index()
    {
        return view('results.index', [
            'patient' => null,
            'labTests' => collect(),
            'radiologyResults' => collect(),
        ]);
    }

    public function search(SearchRequest $request)
    {
        $data = $request->validated();
        $patient = Patient::where('national_id', $data)->first();

        if (!$patient) {
            return back()->withInput()->withErrors([
                'national_id' => 'No results found for this National ID.',
            ]);
        }

        $labTests = $patient->labTests()->latest()->get();
        $radiologyResults = $patient->radiologyResults()->latest()->get();

        return view('results.index', compact('patient', 'labTests', 'radiologyResults'));
    }

    public function download($type, $id)
    {
        if ($type === 'lab') {
            $record = \App\Models\LabTest::findOrFail($id);
        } else {
            $record = \App\Models\RadiologyResult::findOrFail($id);
        }

        if (!$record->file_name) {
            abort(404, 'No file attached.');
        }

        $filePath = public_path('files/' . $record->file_name);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath);
    }

}
