<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentAdminRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Support\AppointmentSecurity;
use App\Support\AuditLogger;
use App\Support\DeletionGuard;
use App\Support\PrivateClinicBookingSupport;
use App\Support\TableFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::query()
            ->with(['patient', 'doctor', 'department'])
            ->latest();

        TableFilters::apply($appointments, $request, [
            'date_column' => 'date',
            'type_column' => 'type',
            'status_column' => 'status',
        ]);

        $filteredAppointmentsCount = (clone $appointments)->count();
        $appointments = $appointments->paginate(10)->appends($request->query());

        Log::debug('Admin appointments index query results.', [
            'filters' => $request->query(),
            'filtered_count' => $filteredAppointmentsCount,
            'page_count' => $appointments->count(),
            'total_appointments' => Appointment::query()->count(),
            'appointments_missing_doctor' => Appointment::query()->whereNull('doctor_id')->count(),
            'appointments_missing_patient' => Appointment::query()->whereNull('patient_id')->count(),
        ]);

        return view('admin.appointments', compact('appointments'));
    }

    public function create()
    {
        $appointment = new Appointment([
            'status' => 'Pending',
            'type' => 'hospital',
        ]);
        $patients = Patient::query()->withoutTrashed()->orderBy('full_name')->get();
        $doctors = Doctor::query()->orderBy('name')->get();

        return view('admin.appointments_form', [
            'appointment' => $appointment,
            'patients' => $patients,
            'doctors' => $doctors,
            'pageTitle' => 'Add New Appointment',
            'pageDescription' => 'Create a new appointment in a dedicated full-page admin workflow.',
            'formAction' => route('admin.appointments.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Create Appointment',
            'isEdit' => false,
        ]);
    }

    public function store(StoreAppointmentAdminRequest $request)
    {
        $data = $request->validated();
        if (empty($data['department_id']) && !empty($data['doctor_id'])) {
            $data['department_id'] = Doctor::find($data['doctor_id'])?->department_id;
        }
        $data['type'] = PrivateClinicBookingSupport::normalizeType($data['type'] ?? 'hospital');
        $doctor = Doctor::with('privateClinic')->findOrFail($data['doctor_id']);

        if ($data['type'] === 'private' && !PrivateClinicBookingSupport::hasPrivateClinic($doctor)) {
            return redirect()->back()->withErrors([
                'type' => 'Private clinic booking is not available for this doctor.',
            ])->withInput();
        }

        $data['payment_amount'] = PrivateClinicBookingSupport::calculateAmount($doctor, $data['type']);
        $data += PrivateClinicBookingSupport::clinicSnapshot($doctor, $data['type']);

        $appointment = DB::transaction(function () use ($doctor, $data) {
            AppointmentSecurity::ensureSlotAvailable($doctor->id, $data['date'], $data['time']);

            $appointment = Appointment::create($data);
            $this->syncPaymentRecord($appointment);

            return $appointment;
        });
        AuditLogger::log('appointment.created', $appointment, ['source' => 'admin']);

        return redirect()->back()->with('success', 'Appointment Added Successfully');
    }

    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        $patients = Patient::query()->withoutTrashed()->orderBy('full_name')->get();
        $doctors = Doctor::query()->orderBy('name')->get();

        return view('admin.appointments_form', [
            'appointment' => $appointment,
            'patients' => $patients,
            'doctors' => $doctors,
            'pageTitle' => 'Edit Appointment',
            'pageDescription' => 'Review and update appointment details in the same dedicated layout used for creating new appointments.',
            'formAction' => route('admin.appointments.update', $appointment->id),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update Appointment',
            'isEdit' => true,
        ]);
    }

    public function update(StoreAppointmentAdminRequest $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $validated = $request->validated();

        if (empty($validated['department_id'])) {
            $validated['department_id'] = Doctor::find($validated['doctor_id'])?->department_id;
        }

        $validated['type'] = PrivateClinicBookingSupport::normalizeType($validated['type'] ?? ($appointment->type ?: 'hospital'));
        $doctor = Doctor::with('privateClinic')->findOrFail($validated['doctor_id']);

        if ($validated['type'] === 'private' && !PrivateClinicBookingSupport::hasPrivateClinic($doctor)) {
            return redirect()->back()->withErrors([
                'type' => 'Private clinic booking is not available for this doctor.',
            ])->withInput();
        }

        $validated['payment_amount'] = PrivateClinicBookingSupport::calculateAmount($doctor, $validated['type']);
        $validated += PrivateClinicBookingSupport::clinicSnapshot($doctor, $validated['type']);
        DB::transaction(function () use ($appointment, $doctor, $validated): void {
            AppointmentSecurity::ensureSlotAvailable($doctor->id, $validated['date'], $validated['time'], $appointment->id);
            $appointment->update($validated);
            $this->syncPaymentRecord($appointment->fresh());
        });
        AuditLogger::log('appointment.updated', $appointment, ['source' => 'admin']);

        return redirect()->route('admin.appointments')->with('success', 'Appointment updated');
    }

    public function delete($id)
    {
        $appointment = Appointment::findOrFail($id);
        DeletionGuard::deleteOne($appointment, 'appointment.deleted', ['source' => 'admin']);

        return redirect()->route('admin.appointments')->with('success', 'Appointment deleted');
    }

    private function syncPaymentRecord(Appointment $appointment): void
    {
        $method = $appointment->payment_method ?: 'pay_at_hospital';
        $status = $appointment->payment_status ?: 'pending';
        $payment = Payment::firstOrNew(['appointment_id' => $appointment->id]);

        $payment->fill([
            'payment_method' => $method,
            'reference_number' => $payment->reference_number ?: 'ADMIN-' . $appointment->id,
            'amount' => $appointment->payment_amount ?? 0,
            'status' => $status,
            'paid_at' => in_array($status, ['confirmed', 'paid'], true) ? ($payment->paid_at ?? now()) : null,
        ])->save();
    }

}
