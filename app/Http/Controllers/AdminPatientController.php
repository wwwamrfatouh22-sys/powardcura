<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Patient;
use App\Support\DeletionGuard;
use App\Support\TableFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminPatientController extends Controller
{
    public function index()
    {
        $patients = Patient::query()->withoutTrashed()->latest();
        TableFilters::apply($patients, request(), [
            'date_column' => 'created_at',
            'status_column' => 'status',
        ]);
        $patients = $patients->paginate(10)->appends(request()->query());

        if ($patients->isEmpty()) {
            Log::debug('Admin patients index returned no rows.', [
                'filters' => request()->query(),
                'active_patients' => Patient::query()->withoutTrashed()->count(),
                'trashed_patients' => Patient::onlyTrashed()->count(),
            ]);
        }

        return view('admin.patients', compact('patients'));
    }
    public function create()
    {
        return view('admin.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'gender' => ['nullable', 'in:male,female'],
        ]);

        Patient::create([
            'full_name' => $request->name,
            'national_id' => $request->national_id,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'gender' => $validated['gender'] ?? null,
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
        DeletionGuard::deleteOne($patient, 'patient.deleted', ['source' => 'admin']);

        return redirect()->route('admin.patients')->with('success', 'Patient deleted successfully');
    }
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('admin.edit', compact('patient'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'gender' => ['nullable', 'in:male,female'],
        ]);

        $patient = Patient::findOrFail($id);

        $patient->update([
            'full_name' => $request->name,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'gender' => $validated['gender'] ?? null,
            'medical_condition' => $request->medical_condition,
            'last_visit' => $request->last_visit,
        ]);

        return redirect()->route('admin.patients');
    }
}
