<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientProfileUpdateRequest;
use App\Models\Appointment;
use App\Models\Medication;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        /** @var \App\Models\Patient|null $patient */
        $patient = Auth::guard('patient')->user();

        abort_unless($patient instanceof Patient, 403);

        $patient->load([
            'user:id,email',
            'appointments' => fn ($query) => $query
                ->with(['doctor.department', 'department', 'rating'])
                ->latest('date')
                ->latest('time'),
            'medications' => fn ($query) => $query
                ->with('doctor:id,name')
                ->latest(),
        ]);

        $nextAppointment = $this->resolveNextAppointment($patient);

        return view('profile.clean_show', compact('patient', 'nextAppointment'));
    }

    public function data(): JsonResponse
    {
        $patient = $this->resolveAuthenticatedPatient();

        $patient->load([
            'user:id,email',
            'appointments' => fn ($query) => $query
                ->with(['doctor.department', 'department', 'rating'])
                ->latest('date')
                ->latest('time'),
        ]);

        return response()->json([
            'data' => [
                'patient' => [
                    'full_name' => $patient->full_name,
                    'email' => $patient->user?->email,
                    'phone' => $patient->phone,
                    'address' => $patient->address,
                    'national_id' => $patient->national_id,
                    'dob' => $patient->dob,
                    'file_number' => $patient->file_number,
                    'gender' => $patient->gender,
                    'age' => $patient->age,
                    'blood_type' => $patient->blood_type,
                    'last_visit' => $patient->last_visit,
                ],
                'next_appointment' => $this->formatAppointment($this->resolveNextAppointment($patient)),
            ],
        ]);
    }

    public function prescriptions(): JsonResponse
    {
        $patient = $this->resolveAuthenticatedPatient();

        $prescriptions = Medication::query()
            ->with('doctor:id,name')
            ->where('patient_id', $patient->id)
            ->latest()
            ->get()
            ->map(fn (Medication $medication) => [
                'id' => $medication->id,
                'doctor_name' => $medication->doctor?->name ?? 'Doctor not assigned',
                'date' => optional($medication->created_at)->toDateString(),
                'medication' => trim($medication->name . ' ' . $medication->dose),
                'notes' => $medication->instructions,
            ]);

        return response()->json(['data' => $prescriptions]);
    }

    public function update(PatientProfileUpdateRequest $request): JsonResponse
    {
        $patient = $this->resolveAuthenticatedPatient();
        $validated = $request->validated();

        DB::transaction(function () use ($patient, $validated) {
            $patient->update([
                'full_name' => trim($validated['full_name']),
                'gender' => $validated['gender'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            if ($patient->user) {
                $patient->user->update([
                    'name' => trim($validated['full_name']),
                    'phone' => $validated['phone'] ?? null,
                ]);
            }
        });

        $patient->refresh()->load('user:id,email');

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => [
                'patient' => [
                    'full_name' => $patient->full_name,
                    'email' => $patient->user?->email,
                    'gender' => $patient->gender,
                    'phone' => $patient->phone,
                    'address' => $patient->address,
                ],
            ],
        ]);
    }

    private function resolveAuthenticatedPatient(): Patient
    {
        /** @var \App\Models\Patient|null $patient */
        $patient = Auth::guard('patient')->user();

        abort_unless($patient instanceof Patient, 403);

        return $patient;
    }

    private function resolveNextAppointment(Patient $patient): ?Appointment
    {
        return $patient->appointments
            ->filter(function (Appointment $appointment): bool {
                if (!in_array($appointment->status, ['Pending', 'Confirmed'], true) || !$appointment->date) {
                    return false;
                }

                $dateTime = Carbon::parse(trim($appointment->date . ' ' . ($appointment->time ?? '00:00')));

                return $dateTime->greaterThanOrEqualTo(now());
            })
            ->sortBy(fn (Appointment $appointment) => ($appointment->date ?? '') . ' ' . ($appointment->time ?? ''))
            ->first();
    }

    private function formatAppointment(?Appointment $appointment): ?array
    {
        if (!$appointment) {
            return null;
        }

        return [
            'id' => $appointment->id,
            'doctor_name' => $appointment->doctor?->name ?? 'Doctor not assigned',
            'department_name' => $appointment->department?->name_en ?? 'Department not assigned',
            'specialization' => $appointment->doctor?->specialization ?? 'Not available',
            'date' => $appointment->date,
            'date_label' => $appointment->date ? Carbon::parse($appointment->date)->format('F j, Y') : 'Not available',
            'time' => $appointment->time ?? 'Not available',
            'reason' => $appointment->reason ?: 'Not available',
            'status' => $appointment->status ?? 'Pending',
        ];
    }
}
