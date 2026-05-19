<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\LabRequest;
use App\Models\Appointment;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use App\Support\AuditLogger;
use App\Support\ProtectedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffRadiologyLabController extends Controller
{
    public function index(Request $request, string $section = 'diagnostics'): View
    {
        $allowedTypes = $this->authorizedTypes();
        $selectedType = $this->selectedType($section, $allowedTypes);

        $requestModel = $selectedType === 'laboratory' ? LabRequest::class : RadiologyRequest::class;
        $appointments = Appointment::query()
            ->with(['patient', 'doctor', 'department'])
            ->whereNotNull('patient_id')
            ->where(function ($builder): void {
                $builder
                    ->whereNotNull('phone')
                    ->orWhereHas('patient', fn ($patientQuery) => $patientQuery->whereNotNull('phone'));
            })
            ->latest()
            ->limit(250)
            ->get();

        $requests = $requestModel::query()
            ->with(['patient', 'doctor', 'appointment.department'])
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        $pendingCount = $requestModel::query()->where('status', 'pending')->count();
        $processingCount = $requestModel::query()->where('status', 'processing')->count();
        $completedCount = $requestModel::query()->where('status', 'completed')->count();
        $recentResults = ($selectedType === 'laboratory' ? LabTest::query() : RadiologyResult::query())
            ->with('patient')
            ->latest()
            ->limit(8)
            ->get();

        return view('staff.radiology_lab', compact(
            'requests',
            'recentResults',
            'pendingCount',
            'processingCount',
            'completedCount',
            'selectedType',
            'allowedTypes',
            'appointments'
        ));
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $this->authorizeType($type);

        $validated = $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'result_file' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,webp,tif,tiff,dcm,doc,docx'],
        ]);

        $appointment = Appointment::query()
            ->with('patient')
            ->whereKey($validated['appointment_id'])
            ->firstOrFail();
        $patient = $appointment->patient;
        abort_unless($patient, 422, 'Selected appointment does not have a linked patient.');

        $path = ProtectedFile::storeMedicalResult($request->file('result_file'), $type);
        $staffId = Auth::guard('staff')->id();
        $phone = $appointment->phone ?: $patient->phone;

        $record = $type === 'laboratory'
            ? LabTest::query()->create([
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
                'patient_phone' => $phone,
                'uploaded_by_staff_id' => $staffId,
                'result_type' => 'laboratory',
                'title' => trim($validated['title']),
                'description' => trim((string) ($validated['notes'] ?? '')),
                'notes' => $validated['notes'] ?? null,
                'test_date' => now()->toDateString(),
                'file_name' => $path,
            ])
            : RadiologyResult::query()->create([
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
                'patient_phone' => $phone,
                'uploaded_by_staff_id' => $staffId,
                'result_type' => 'radiology',
                'title' => trim($validated['title']),
                'description' => trim((string) ($validated['notes'] ?? '')),
                'notes' => $validated['notes'] ?? null,
                'scan_date' => now()->toDateString(),
                'file_name' => $path,
            ]);

        AuditLogger::log('medical_result.uploaded', $record, [
            'source' => 'staff_direct_upload',
            'type' => $type,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'patient_phone' => $phone,
            'uploaded_by_staff_id' => $staffId,
        ]);

        return back()->with('success', 'Result uploaded successfully.');
    }

    public function process(Request $request, string $type, int $id): RedirectResponse
    {
        $this->authorizeType($type);

        $diagnosticRequest = $this->requestQuery($type)->findOrFail($id);
        abort_unless($diagnosticRequest->status !== 'completed', 422, 'Completed requests cannot be moved back into processing.');

        $diagnosticRequest->update(['status' => 'processing']);

        AuditLogger::log('diagnostic_request.processing', $diagnosticRequest->fresh(), [
            'type' => $type,
            'staff_id' => Auth::guard('staff')->id(),
        ]);

        return back()->with('success', 'Request marked as processing.');
    }

    public function complete(Request $request, string $type, int $id): RedirectResponse
    {
        $this->authorizeType($type);

        $validated = $request->validate([
            'result_file' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,webp,tif,tiff,dcm,doc,docx'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $diagnosticRequest = $this->requestQuery($type)->with(['patient', 'appointment'])->findOrFail($id);
        $path = ProtectedFile::storeMedicalResult($request->file('result_file'), $type);
        $staffId = Auth::guard('staff')->id();
        $notes = $validated['notes'] ?? $diagnosticRequest->notes;

        $result = $type === 'laboratory'
            ? LabTest::query()->create([
                'patient_id' => $diagnosticRequest->patient_id,
                'appointment_id' => $diagnosticRequest->appointment_id,
                'patient_phone' => $diagnosticRequest->phone,
                'uploaded_by_staff_id' => $staffId,
                'result_type' => 'laboratory',
                'title' => $diagnosticRequest->request_type,
                'description' => trim((string) $notes),
                'notes' => $notes,
                'test_date' => now()->toDateString(),
                'file_name' => $path,
            ])
            : RadiologyResult::query()->create([
                'patient_id' => $diagnosticRequest->patient_id,
                'appointment_id' => $diagnosticRequest->appointment_id,
                'patient_phone' => $diagnosticRequest->phone,
                'uploaded_by_staff_id' => $staffId,
                'result_type' => 'radiology',
                'title' => $diagnosticRequest->request_type,
                'description' => trim((string) $notes),
                'notes' => $notes,
                'scan_date' => now()->toDateString(),
                'file_name' => $path,
            ]);

        $diagnosticRequest->update([
            'status' => 'completed',
            'uploaded_result' => $path,
            'completed_by_staff_id' => $staffId,
            'completed_at' => now(),
        ]);

        AuditLogger::log('diagnostic_request.completed', $diagnosticRequest->fresh(), [
            'type' => $type,
            'result_id' => $result->id,
            'staff_id' => $staffId,
        ]);

        return back()->with('success', 'Result uploaded and request completed.');
    }

    private function selectedType(string $section, array $allowedTypes): string
    {
        $selected = match ($section) {
            'laboratory', 'lab' => 'laboratory',
            'radiology' => 'radiology',
            default => $allowedTypes[0],
        };

        abort_unless(in_array($selected, $allowedTypes, true), 403);

        return $selected;
    }

    private function authorizedTypes(): array
    {
        $role = Auth::guard('staff')->user()?->role;

        return match ($role) {
            'lab' => ['laboratory'],
            'laboratory' => ['laboratory'],
            'radiology' => ['radiology'],
            'radiology_lab' => ['laboratory', 'radiology'],
            default => abort(403),
        };
    }

    private function authorizeType(string $type): void
    {
        abort_unless(in_array($type, $this->authorizedTypes(), true), 403);
    }

    private function requestQuery(string $type)
    {
        abort_unless(in_array($type, ['laboratory', 'radiology'], true), 404);

        return $type === 'laboratory'
            ? LabRequest::query()
            : RadiologyRequest::query();
    }
}
