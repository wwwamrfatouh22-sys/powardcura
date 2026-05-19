<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Department;
use App\Support\NormalizesDepartments;
use App\Support\AuditLogger;
use App\Support\DeletionGuard;
use App\Support\PrivateClinicBookingSupport;
use App\Support\TableFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminDoctorController extends Controller
{
    public function index(Request $request)
    {
        NormalizesDepartments::sync();

        $doctors = Doctor::query()
            ->with(['department', 'privateClinic'])
            ->latest();

        TableFilters::apply($doctors, $request, [
            'date_column' => 'created_at',
            'status_column' => 'status',
        ]);

        $filteredDoctorsCount = (clone $doctors)->count();
        $doctors = $doctors->paginate(10)->appends($request->query());

        Log::debug('Admin doctors index query results.', [
            'filters' => $request->query(),
            'filtered_count' => $filteredDoctorsCount,
            'page_count' => $doctors->count(),
            'total_doctors' => Doctor::query()->count(),
        ]);

        return view('admin.doctors', compact('doctors'));
    }

    public function create()
    {
        NormalizesDepartments::sync();

        $departments = Department::query()->orderBy('name_en')->get();
        return view('admin.doctors_create', compact('departments'));
    }

    public function store(Request $request)
    {
        NormalizesDepartments::sync();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'email' => 'nullable|email|max:255|unique:doctors,email',
            'status' => 'required|in:Available,Busy',
            'department_id' => 'required|exists:departments,id',
            'has_private_clinic' => 'nullable|boolean',
            'clinic_name' => 'nullable|string|max:255',
            'clinic_address' => 'nullable|string|max:255',
            'clinic_phone' => 'nullable|string|max:50',
            'clinic_fee' => 'nullable|numeric|min:0',
            'clinic_available_days' => 'nullable|array',
            'clinic_available_days.*' => ['nullable', Rule::in(PrivateClinicBookingSupport::WEEK_DAYS)],
            'clinic_available_times' => 'nullable|string|max:500',
            'clinic_notes' => 'nullable|string|max:2000',
        ]);
        $clinicPayload = $this->preparePrivateClinicPayload($validated, $request);

        $doctor = Doctor::create([
            'name' => trim($validated['name']),
            'specialization' => trim($validated['specialization']),
            'experience' => $validated['experience'],
            'email' => isset($validated['email']) ? trim((string) $validated['email']) : null,
            'status' => $validated['status'],
            'department_id' => $validated['department_id'],
            'password' => Hash::make(Str::password(32)),
            'rating' => 4.5,
            'image' => null,
            'has_private_clinic' => $request->boolean('has_private_clinic', true),
        ]);

        $this->syncPrivateClinic($doctor, $clinicPayload);
        AuditLogger::log('doctor.created', $doctor, ['source' => 'admin']);

        return redirect()
            ->route('admin.doctors')
            ->with('success', "Doctor {$doctor->name} added successfully. Send a password reset or invitation email before first login.");
    }

    public function edit($id)
    {
        NormalizesDepartments::sync();

        $doctor = Doctor::with('privateClinic')->findOrFail($id);
        $departments = Department::orderBy('name_en')->get();
        return view('admin.doctors_edit', compact('doctor', 'departments'));
    }

    public function update(Request $request, $id)
    {
        NormalizesDepartments::sync();

        $doctor = Doctor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('doctors', 'email')->ignore($doctor->id)],
            'status' => 'required|in:Available,Busy',
            'department_id' => 'required|exists:departments,id',
            'has_private_clinic' => 'nullable|boolean',
            'clinic_name' => 'nullable|string|max:255',
            'clinic_address' => 'nullable|string|max:255',
            'clinic_phone' => 'nullable|string|max:50',
            'clinic_fee' => 'nullable|numeric|min:0',
            'clinic_available_days' => 'nullable|array',
            'clinic_available_days.*' => ['nullable', Rule::in(PrivateClinicBookingSupport::WEEK_DAYS)],
            'clinic_available_times' => 'nullable|string|max:500',
            'clinic_notes' => 'nullable|string|max:2000',
        ]);
        $clinicPayload = $this->preparePrivateClinicPayload($validated, $request);

        $doctor->update([
            'name' => trim($validated['name']),
            'specialization' => trim($validated['specialization']),
            'experience' => $validated['experience'],
            'email' => isset($validated['email']) ? trim((string) $validated['email']) : null,
            'status' => $validated['status'],
            'department_id' => $validated['department_id'],
            'has_private_clinic' => $request->boolean('has_private_clinic', true),
        ]);

        $this->syncPrivateClinic($doctor, $clinicPayload);
        AuditLogger::log('doctor.updated', $doctor, ['source' => 'admin']);

        return redirect()->route('admin.doctors')->with('success', 'Doctor updated successfully');
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        DeletionGuard::deleteOne($doctor, 'doctor.deleted', ['source' => 'admin']);

        return redirect()->route('admin.doctors')->with('success', 'Doctor deleted');
    }

    private function preparePrivateClinicPayload(array $validated, Request $request): ?array
    {
        if (!$request->boolean('has_private_clinic', true)) {
            return null;
        }

        $days = PrivateClinicBookingSupport::normalizeDays($validated['clinic_available_days'] ?? []);
        $times = PrivateClinicBookingSupport::parseTimesString($request->input('clinic_available_times'));

        $payload = [
            'clinic_name' => isset($validated['clinic_name']) ? trim((string) $validated['clinic_name']) : '',
            'clinic_address' => isset($validated['clinic_address']) ? trim((string) $validated['clinic_address']) : '',
            'clinic_phone' => isset($validated['clinic_phone']) ? trim((string) $validated['clinic_phone']) : '',
            'clinic_fee' => $validated['clinic_fee'] ?? null,
            'available_days' => $days,
            'available_times' => $times,
            'notes' => isset($validated['clinic_notes']) ? trim((string) $validated['clinic_notes']) : '',
        ];

        $hasClinicData = $payload['clinic_name'] !== ''
            || $payload['clinic_address'] !== ''
            || $payload['clinic_phone'] !== ''
            || $payload['clinic_fee'] !== null
            || $payload['notes'] !== ''
            || $payload['available_days'] !== []
            || $payload['available_times'] !== [];

        if (!$hasClinicData) {
            return null;
        }

        $errors = [];

        if ($payload['clinic_name'] === '') {
            $errors['clinic_name'] = 'Clinic name is required when configuring a private clinic.';
        }

        if ($payload['clinic_address'] === '') {
            $errors['clinic_address'] = 'Clinic address is required when configuring a private clinic.';
        }

        if ($payload['clinic_phone'] === '') {
            $errors['clinic_phone'] = 'Clinic phone is required when configuring a private clinic.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $payload;
    }

    private function syncPrivateClinic(Doctor $doctor, ?array $payload): void
    {
        if ($payload === null) {
            $privateClinic = $doctor->privateClinic()->first();

            if ($privateClinic) {
                DeletionGuard::deleteOne($privateClinic, 'private_clinic.deleted', ['source' => 'admin']);
            }

            return;
        }

        $doctor->privateClinic()->updateOrCreate(
            ['doctor_id' => $doctor->id],
            [
                'clinic_name' => $payload['clinic_name'],
                'clinic_address' => $payload['clinic_address'],
                'clinic_phone' => $payload['clinic_phone'],
                'clinic_fee' => $payload['clinic_fee'],
                'available_days' => $payload['available_days'] !== [] ? $payload['available_days'] : null,
                'available_times' => $payload['available_times'] !== [] ? $payload['available_times'] : null,
                'notes' => $payload['notes'] !== '' ? $payload['notes'] : null,
            ]
        );
    }
}
