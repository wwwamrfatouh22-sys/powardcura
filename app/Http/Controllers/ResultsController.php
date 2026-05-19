<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\LabTest;
use App\Models\RadiologyResult;
use App\Models\Patient;
use App\Policies\SensitiveFilePolicy;
use App\Support\AuditLogger;
use App\Support\AuthContext;
use App\Support\ProtectedFile;
use Illuminate\View\View;

class ResultsController extends Controller
{
    public function index()
    {
        $patient = $this->resolvePatient();

        return $this->resultsView($patient);
    }

    public function search(SearchRequest $request)
    {
        $patient = $this->resolvePatient();
        $data = $request->validated();

        if ($data['national_id'] !== $patient->national_id) {
            return back()->withInput()->withErrors([
                'national_id' => 'You can only access medical results for your authenticated account.',
            ]);
        }

        return $this->resultsView($patient);
    }

    public function admin(Patient $patient): View
    {
        abort_unless(AuthContext::role() === 'admin', 403);

        return $this->resultsView($patient);
    }

    public function download($type, $id)
    {
        $record = $type === 'lab'
            ? LabTest::findOrFail($id)
            : RadiologyResult::findOrFail($id);

        abort_unless((new SensitiveFilePolicy())->viewMedicalResult($record), 403);
        abort_unless($record->file_name, 404, 'No file attached.');

        AuditLogger::log('medical_result.file_accessed', $record, [
            'result_type' => $type,
            'file_name' => $record->file_name,
        ]);

        return ProtectedFile::download($this->medicalResultPath($type, $record->file_name), 'medical-result-' . $record->id);
    }

    private function resolvePatient(): Patient
    {
        if (AuthContext::role() === 'admin') {
            $patientId = request()->integer('patient_id');
            abort_unless($patientId > 0, 422, 'Patient ID is required for admin result access.');

            return Patient::findOrFail($patientId);
        }

        abort_unless(AuthContext::role() === 'patient', 403);

        return Patient::findOrFail(AuthContext::id());
    }

    private function resultsView(Patient $patient): View
    {
        $labTests = $patient->labTests()->latest()->get();
        $radiologyResults = $patient->radiologyResults()->latest()->get();

        return view('results.index', compact('patient', 'labTests', 'radiologyResults'));
    }

    private function medicalResultPath(string $type, string $fileName): string
    {
        if (str_contains($fileName, '/')) {
            return $fileName;
        }

        $folder = $type === 'lab' ? 'lab' : 'radiology';

        return 'medical-results/' . $folder . '/' . basename($fileName);
    }
}
