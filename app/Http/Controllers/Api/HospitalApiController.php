<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Payment;
use App\Support\AppointmentSecurity;
use App\Support\AuditLogger;
use App\Support\AuthContext;
use App\Support\DeletionGuard;
use App\Support\DepartmentCatalog;
use App\Support\NormalizesDepartments;
use App\Support\PrivateClinicBookingSupport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HospitalApiController extends Controller
{
    public function doctors(): JsonResponse
    {
        NormalizesDepartments::sync();

        $doctors = Doctor::with(['department', 'privateClinic'])
            ->latest()
            ->get()
            ->map(function (Doctor $doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'email' => $doctor->email,
                    'specialization' => $doctor->specialization,
                    'experience' => $doctor->experience,
                    'status' => $doctor->status,
                    'department_id' => $doctor->department_id,
                    'department_name' => $doctor->department?->name_en,
                    'clinic_name' => $doctor->privateClinic?->clinic_name,
                    'has_private_clinic' => PrivateClinicBookingSupport::hasPrivateClinic($doctor),
                ];
            });

        return response()->json(['data' => $doctors]);
    }

    public function storeDoctor(Request $request): JsonResponse
    {
        NormalizesDepartments::sync();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'experience' => ['required', 'integer', 'min:0'],
            'email' => ['nullable', 'email', 'max:255', 'unique:doctors,email'],
            'status' => ['required', 'in:Available,Busy'],
            'department_id' => ['required', 'exists:departments,id'],
            'has_private_clinic' => ['nullable', 'boolean'],
        ]);

        $doctor = Doctor::create([
            'name' => trim($validated['name']),
            'specialization' => trim($validated['specialization']),
            'experience' => $validated['experience'],
            'email' => isset($validated['email']) ? trim((string) $validated['email']) : null,
            'status' => $validated['status'],
            'department_id' => $validated['department_id'],
            'has_private_clinic' => (bool) ($validated['has_private_clinic'] ?? true),
            'password' => Hash::make(Str::password(32)),
            'rating' => 4.5,
            'image' => null,
        ]);
        AuditLogger::log('doctor.created', $doctor, ['source' => 'api']);

        return response()->json([
            'message' => 'Doctor created successfully. Send a password reset or invitation email before first login.',
            'data' => $doctor->load('department'),
        ], 201);
    }

    public function updateDoctor(Request $request, Doctor $doctor): JsonResponse
    {
        NormalizesDepartments::sync();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'specialization' => ['sometimes', 'required', 'string', 'max:255'],
            'experience' => ['sometimes', 'required', 'integer', 'min:0'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('doctors', 'email')->ignore($doctor->id),
            ],
            'status' => ['sometimes', 'required', 'in:Available,Busy'],
            'department_id' => ['sometimes', 'required', 'exists:departments,id'],
            'has_private_clinic' => ['nullable', 'boolean'],
        ]);

        $doctor->update([
            'name' => array_key_exists('name', $validated) ? trim($validated['name']) : $doctor->name,
            'specialization' => array_key_exists('specialization', $validated)
                ? trim($validated['specialization'])
                : $doctor->specialization,
            'experience' => $validated['experience'] ?? $doctor->experience,
            'email' => array_key_exists('email', $validated)
                ? (isset($validated['email']) ? trim((string) $validated['email']) : null)
                : $doctor->email,
            'status' => $validated['status'] ?? $doctor->status,
            'department_id' => $validated['department_id'] ?? $doctor->department_id,
            'has_private_clinic' => array_key_exists('has_private_clinic', $validated)
                ? (bool) $validated['has_private_clinic']
                : $doctor->has_private_clinic,
        ]);
        AuditLogger::log('doctor.updated', $doctor, ['source' => 'api']);

        return response()->json([
            'message' => 'Doctor updated successfully.',
            'data' => $doctor->fresh()->load('department'),
        ]);
    }

    public function deleteDoctor(Doctor $doctor): JsonResponse
    {
        DeletionGuard::deleteOne($doctor, 'doctor.deleted', ['source' => 'api']);

        return response()->json(['message' => 'Doctor deleted successfully.']);
    }

    public function departments(): JsonResponse
    {
        NormalizesDepartments::sync();

        $staffCountResolver = static function (Department $department): int {
            if (Schema::hasTable('staff') && Schema::hasColumn('staff', 'department_id')) {
                return (int) \DB::table('staff')->where('department_id', $department->id)->count();
            }

            return 0;
        };

        $departments = Department::with(['doctor', 'doctors'])
            ->withCount('doctors')
            ->orderBy('name_en')
            ->get()
            ->map(function (Department $department) use ($staffCountResolver) {
                return [
                    'id' => $department->id,
                    'name_en' => $department->name_en,
                    'head_name' => $department->head_name ?: $department->doctor?->name,
                    'doctor_name' => $department->doctor?->name,
                    'status' => $department->status ?: 'active',
                    'doctors_count' => $department->doctors_count,
                    'staff_count' => $staffCountResolver($department),
                    'doctor_id' => $department->doctor_id,
                    'doctors' => $department->doctors
                        ->sortBy('name')
                        ->values()
                        ->map(fn (Doctor $doctor) => [
                            'id' => $doctor->id,
                            'name' => $doctor->name,
                            'specialization' => $doctor->specialization,
                            'experience' => $doctor->experience,
                            'email' => $doctor->email,
                            'status' => $doctor->status,
                        ]),
                ];
            });

        return response()->json(['data' => $departments]);
    }

    public function storeDepartment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'head_name' => ['nullable', 'string', 'max:255'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $pair = DepartmentCatalog::pairFromInput($validated['name_en'], $request->input('name_ar'));

        if ($pair === null) {
            return response()->json([
                'message' => 'Select a valid department from the required list.',
                'errors' => ['name_en' => ['Select a valid department from the required list.']],
            ], 422);
        }

        if (Department::query()->where('name_en', $pair['name_en'])->orWhere('name_ar', $pair['name_ar'])->exists()) {
            return response()->json([
                'message' => 'Department already exists.',
                'errors' => ['name_en' => ['That department already exists in English.']],
            ], 422);
        }

        $department = Department::create([
            'name_en' => $pair['name_en'],
            'name_ar' => $pair['name_ar'],
            'head_name' => $validated['head_name'] ?? null,
            'doctor_id' => $validated['doctor_id'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);
        AuditLogger::log('department.created', $department, ['source' => 'api']);

        return response()->json([
            'message' => 'Department created successfully.',
            'data' => $department,
        ], 201);
    }

    public function updateDepartment(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'name_en' => ['sometimes', 'required', 'string', 'max:255'],
            'head_name' => ['nullable', 'string', 'max:255'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $pair = array_key_exists('name_en', $validated)
            ? DepartmentCatalog::pairFromInput($validated['name_en'], $request->input('name_ar'))
            : ['name_en' => $department->name_en, 'name_ar' => $department->name_ar];

        if ($pair === null) {
            return response()->json([
                'message' => 'Select a valid department from the required list.',
                'errors' => ['name_en' => ['Select a valid department from the required list.']],
            ], 422);
        }

        $duplicateExists = Department::query()
            ->where(function ($query) use ($pair) {
                $query->where('name_en', $pair['name_en'])
                    ->orWhere('name_ar', $pair['name_ar']);
            })
            ->where('id', '!=', $department->id)
            ->exists();

        if ($duplicateExists) {
            return response()->json([
                'message' => 'Department already exists.',
                'errors' => ['name_en' => ['That department already exists in English.']],
            ], 422);
        }

        $department->update([
            'name_en' => $pair['name_en'],
            'name_ar' => $pair['name_ar'],
            'head_name' => $validated['head_name'] ?? $department->head_name,
            'doctor_id' => array_key_exists('doctor_id', $validated) ? $validated['doctor_id'] : $department->doctor_id,
            'status' => $validated['status'] ?? $department->status,
        ]);
        AuditLogger::log('department.updated', $department, ['source' => 'api']);

        return response()->json([
            'message' => 'Department updated successfully.',
            'data' => $department->fresh(),
        ]);
    }

    public function deleteDepartment(Department $department): JsonResponse
    {
        DeletionGuard::deleteOne($department, 'department.deleted', ['source' => 'api']);

        return response()->json(['message' => 'Department deleted successfully.']);
    }

    public function appointments(): JsonResponse
    {
        $appointmentsQuery = Appointment::with(['doctor', 'patient', 'department'])->latest();

        if (AuthContext::role() === 'doctor') {
            $appointmentsQuery->where('doctor_id', AuthContext::id());
        }

        $appointments = $appointmentsQuery->get()
            ->map(function (Appointment $appointment) {
                $patientName = trim((string) $appointment->first_name . ' ' . (string) $appointment->last_name);
                if ($patientName === '' && $appointment->patient) {
                    $patientName = (string) $appointment->patient->full_name;
                }

                return [
                    'id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'patient_name' => $patientName !== '' ? $patientName : 'Unknown',
                    'doctor_id' => $appointment->doctor_id,
                    'doctor_name' => $appointment->doctor?->name,
                    'department_id' => $appointment->department_id,
                    'department_name' => $appointment->department?->name_en,
                    'first_name' => $appointment->first_name,
                    'last_name' => $appointment->last_name,
                    'date' => $appointment->date,
                    'time' => $appointment->time,
                    'status' => $appointment->status ?? 'Pending',
                    'type' => $appointment->type,
                    'type_label' => PrivateClinicBookingSupport::typeLabel($appointment->type),
                    'clinic_name' => $appointment->clinic_name,
                    'email' => $appointment->email,
                    'phone' => $appointment->phone,
                    'reason' => $appointment->reason,
                    'payment_method' => $appointment->payment_method,
                    'payment_status' => $appointment->payment_status,
                ];
            });

        return response()->json(['data' => $appointments]);
    }

    public function doctorAppointments(Doctor $doctor): JsonResponse
    {
        abort_unless(AuthContext::role() !== 'doctor' || (int) $doctor->id === (int) AuthContext::id(), 403);

        $appointments = Appointment::with(['doctor', 'patient', 'department'])
            ->where('doctor_id', $doctor->id)
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(function (Appointment $appointment) {
                $patientName = trim((string) $appointment->first_name . ' ' . (string) $appointment->last_name);
                if ($patientName === '' && $appointment->patient) {
                    $patientName = (string) $appointment->patient->full_name;
                }

                return [
                    'id' => $appointment->id,
                    'doctor_id' => $appointment->doctor_id,
                    'patient_name' => $patientName !== '' ? $patientName : 'Unknown Patient',
                    'date' => $appointment->date,
                    'time' => $appointment->time,
                    'reason' => $appointment->reason ?: 'General consultation',
                    'type' => $appointment->type ?: 'hospital',
                    'status' => $appointment->status ?: 'Pending',
                    'department_name' => $appointment->department?->name_en,
                ];
            });

        return response()->json([
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
            ],
            'data' => $appointments,
        ]);
    }

    public function storeAppointment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => ['nullable', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'first_name' => ['required_without:patient_id', 'nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date'],
            'time' => ['required'],
            'status' => ['nullable', 'in:Pending,Confirmed,Completed'],
            'type' => ['nullable', 'in:hospital,private'],
            'payment_method' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['type'] = PrivateClinicBookingSupport::normalizeType($validated['type'] ?? 'hospital');
        $doctor = Doctor::with('privateClinic')->findOrFail($validated['doctor_id']);

        if ($validated['type'] === 'private' && !PrivateClinicBookingSupport::hasPrivateClinic($doctor)) {
            return response()->json([
                'message' => 'Private clinic booking is not available for this doctor.',
                'errors' => ['type' => ['Private clinic booking is not available for this doctor.']],
            ], 422);
        }

        $validated['department_id'] = $validated['department_id'] ?? $doctor->department_id;
        $validated['payment_amount'] = PrivateClinicBookingSupport::calculateAmount($doctor, $validated['type']);
        $validated['time'] = AppointmentSecurity::normalizeTime((string) $validated['time']);
        AppointmentSecurity::ensureSlotAvailable($doctor->id, $validated['date'], $validated['time']);
        $validated += PrivateClinicBookingSupport::clinicSnapshot($doctor, $validated['type']);

        $appointment = DB::transaction(function () use ($doctor, $validated) {
            AppointmentSecurity::ensureSlotAvailable($doctor->id, $validated['date'], $validated['time']);

            $appointment = Appointment::create($validated + ['status' => $validated['status'] ?? 'Pending']);
            $this->syncPaymentRecord($appointment);

            return $appointment;
        });
        AuditLogger::log('appointment.created', $appointment, ['source' => 'api']);

        return response()->json([
            'message' => 'Appointment created successfully.',
            'data' => $appointment->load(['doctor', 'patient', 'department']),
        ], 201);
    }

    public function updateAppointment(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => ['nullable', 'exists:patients,id'],
            'doctor_id' => ['sometimes', 'required', 'exists:doctors,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'date' => ['sometimes', 'required', 'date'],
            'time' => ['sometimes', 'required'],
            'status' => ['nullable', 'in:Pending,Confirmed,Completed'],
            'type' => ['nullable', 'in:hospital,private'],
            'payment_method' => ['nullable', 'string', 'max:100'],
        ]);

        $doctor = array_key_exists('doctor_id', $validated)
            ? Doctor::with('privateClinic')->findOrFail($validated['doctor_id'])
            : $appointment->doctor()->with('privateClinic')->firstOrFail();

        $validated['type'] = PrivateClinicBookingSupport::normalizeType($validated['type'] ?? $appointment->type ?? 'hospital');

        if ($validated['type'] === 'private' && !PrivateClinicBookingSupport::hasPrivateClinic($doctor)) {
            return response()->json([
                'message' => 'Private clinic booking is not available for this doctor.',
                'errors' => ['type' => ['Private clinic booking is not available for this doctor.']],
            ], 422);
        }

        $validated['department_id'] = $validated['department_id'] ?? $doctor->department_id ?? $appointment->department_id;
        $validated['payment_amount'] = PrivateClinicBookingSupport::calculateAmount($doctor, $validated['type']);
        if (array_key_exists('time', $validated)) {
            $validated['time'] = AppointmentSecurity::normalizeTime((string) $validated['time']);
        }
        $validated += PrivateClinicBookingSupport::clinicSnapshot($doctor, $validated['type']);
        DB::transaction(function () use ($appointment, $doctor, $validated): void {
            AppointmentSecurity::ensureSlotAvailable(
                $doctor->id,
                $validated['date'] ?? $appointment->date,
                $validated['time'] ?? $appointment->time,
                $appointment->id
            );
            $appointment->update($validated);
            $this->syncPaymentRecord($appointment->fresh());
        });
        AuditLogger::log('appointment.updated', $appointment, ['source' => 'api']);

        return response()->json([
            'message' => 'Appointment updated successfully.',
            'data' => $appointment->fresh()->load(['doctor', 'patient', 'department']),
        ]);
    }

    public function deleteAppointment(Appointment $appointment): JsonResponse
    {
        DeletionGuard::deleteOne($appointment, 'appointment.deleted', ['source' => 'api']);

        return response()->json(['message' => 'Appointment deleted successfully.']);
    }

    private function syncPaymentRecord(Appointment $appointment): void
    {
        $payment = Payment::firstOrNew(['appointment_id' => $appointment->id]);

        $payment->fill([
            'payment_method' => $appointment->payment_method ?: 'pay_at_hospital',
            'reference_number' => $payment->reference_number ?: 'API-' . $appointment->id,
            'amount' => $appointment->payment_amount ?? 0,
            'status' => $appointment->payment_status ?: 'pending',
        ])->save();
    }
}
