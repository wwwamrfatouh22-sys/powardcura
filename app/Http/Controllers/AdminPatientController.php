<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Patient;
use Illuminate\Http\Request;

class AdminPatientController extends Controller
{
    public function index()
    {
        $patients = Patient::latest()->get();
        return view('admin.patients', compact('patients'));
    }
    public function create()
    {
        return view('admin.create');
    }


    public function store(Request $request)
    {
        Patient::create([
            'full_name' => $request->name,
            'national_id' => $request->national_id,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'file_number' => $request->file_number,
            'medical_condition' => $request->medical_condition,

            'last_visit' => $request->last_visit
                ? Carbon::parse($request->last_visit)->format('Y-m-d H:i:s')
                : null,
        ]);

        return redirect()->route('admin.patients')
            ->with('success', 'Patient added successfully ✅');
    }
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return redirect()->route('admin.patients')->with('success', 'Patient deleted successfully');
    }
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('admin.edit', compact('patient'));
    }
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $patient->update([
            'full_name' => $request->name,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'medical_condition' => $request->medical_condition,
            'last_visit' => $request->last_visit,
        ]);

        return redirect()->route('admin.patients');
    }
}
