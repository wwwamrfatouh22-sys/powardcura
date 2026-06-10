<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\ConfirmAppointmentPaymentRequest;
use App\Models\Appointment;
use App\Models\BlockedTime;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Payment;
use App\Services\Payments\FawryPaymentService;
use App\Services\Scheduling\SlotGenerationService;
use App\Support\AppointmentSecurity;
use App\Support\AuditLogger;
use App\Support\PrivateClinicBookingSupport;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Throwable;

class AppointmentController extends Controller
{
    public function create(Doctor $doctor, string $time): View
    {
        $doctor->loadMissing(['department', 'privateClinic']);
        $hasPrivateClinic = PrivateClinicBookingSupport::hasPrivateClinic($doctor);
        $selectedDate = request('date', now()->toDateString());
        $selectedType = PrivateClinicBookingSupport::normalizeType(request('type'));

        if ($selectedType === 'private' && !$hasPrivateClinic) {
            $selectedType = 'hospital';
        }

        $estimatedAmount = PrivateClinicBookingSupport::calculateAmount($doctor, $selectedType);
        $normalizedTime = AppointmentSecurity::normalizeTime($time);
        $slotStillAvailable = true;

        try {
            $this->ensureGeneratedSlotAvailable($doctor, $selectedType, $selectedDate, $normalizedTime);
        } catch (ValidationException) {
            $slotStillAvailable = false;
        }

        return view('appointments.create', compact(
            'doctor',
            'time',
            'selectedDate',
            'estimatedAmount',
            'selectedType',
            'hasPrivateClinic',
            'slotStillAvailable'
        ));
    }

    public function review(AppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $doctor = Doctor::with('privateClinic')->findOrFail($data['doctor_id']);
        $type = PrivateClinicBookingSupport::normalizeType($data['type']);
        $normalizedTime = AppointmentSecurity::normalizeTime((string) $data['time']);
        $selectedDate = $data['date'] ?? now()->toDateString();

        if ($type === 'private' && !PrivateClinicBookingSupport::hasPrivateClinic($doctor)) {
            return back()
                ->withErrors(['type' => 'This doctor does not currently have private clinic booking details configured.'])
                ->withInput();
        }

        if (!PrivateClinicBookingSupport::isDateAvailable($doctor, $type, $selectedDate)) {
            return back()
                ->withErrors(['date' => 'The selected date is not available for this appointment type.'])
                ->withInput();
        }

        try {
            $this->ensureGeneratedSlotAvailable($doctor, $type, $selectedDate, $normalizedTime);
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }

        $draft = [
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'reason' => $data['reason'] ?? null,
            'time' => $normalizedTime,
            'date' => $selectedDate,
            'type' => $type,
        ];

        $draft['payment_amount'] = PrivateClinicBookingSupport::calculateAmount($doctor, $draft['type']);
        $draft += PrivateClinicBookingSupport::clinicSnapshot($doctor, $draft['type']);

        Session::put('booking_draft', $draft);

        return redirect()->route('appointments.payment');
    }

    public function payment(): RedirectResponse|View
    {
        $draft = Session::get('booking_draft');

        if (!$draft) {
            return redirect()->to($this->bookingStartUrl())->withErrors([
                'booking' => 'Please start booking first by selecting doctor, date and time.',
            ]);
        }

        $doctor = Doctor::with(['department', 'privateClinic'])->findOrFail($draft['doctor_id']);

        return view('appointments.payment', [
            'draft' => $draft,
            'doctor' => $doctor,
        ]);
    }

    public function confirm(ConfirmAppointmentPaymentRequest $request): RedirectResponse
    {
        $draft = Session::get('booking_draft');

        if (!$draft) {
            return redirect()->to($this->bookingStartUrl())->withErrors([
                'booking' => 'Booking session expired. Please start again.',
            ]);
        }

        $doctor = Doctor::with('privateClinic')->findOrFail($draft['doctor_id']);
        if ($draft['type'] === 'private' && !PrivateClinicBookingSupport::hasPrivateClinic($doctor)) {
            Session::forget('booking_draft');
            return redirect()
                ->route('doctors.show', ['doctor' => $doctor->id, 'date' => $draft['date'], 'type' => $draft['type']])
                ->withErrors(['type' => 'Private clinic details are no longer available for this doctor.']);
        }

        $paymentMethod = $request->validated('payment_method');
        $payAtHospital = $paymentMethod === 'pay_at_hospital';
        $fawry = app(FawryPaymentService::class);

        if (! $payAtHospital && ! $fawry->isConfigured()) {
            return back()
                ->withErrors(['payment_method' => 'Online payment is temporarily unavailable. Please try again or choose pay at hospital.'])
                ->withInput();
        }

        try {
            Log::debug('Booking confirm: before slot lock.', $this->bookingLogContext($draft, [
                'payment_method' => $paymentMethod,
                'patient_id' => auth('patient')->id(),
            ]));

            $slotLockName = $this->acquireSlotLock($doctor, $draft['type'], $draft['date'], $draft['time']);
            $this->ensureGeneratedSlotAvailable($doctor, $draft['type'], $draft['date'], $draft['time']);

            Log::debug('Booking confirm: before transaction.', $this->bookingLogContext($draft, [
                'payment_method' => $paymentMethod,
                'slot_lock' => $slotLockName,
            ]));

            $appointment = DB::transaction(function () use ($doctor, $draft, $paymentMethod, $payAtHospital, $fawry) {
                $this->lockCurrentSlotRows($doctor, $draft['type'], $draft['date'], $draft['time']);
                $this->ensureGeneratedSlotAvailable($doctor, $draft['type'], $draft['date'], $draft['time']);

                $appointment = Appointment::create([
                    'patient_id' => auth('patient')->id(),
                    'doctor_id' => $doctor->id,
                    'department_id' => $doctor->department_id,
                    'date' => $draft['date'],
                    'time' => $draft['time'],
                    'status' => $payAtHospital ? 'Confirmed' : 'Pending',
                    'first_name' => $draft['first_name'] ?? '',
                    'last_name' => $draft['last_name'] ?? '',
                    'email' => $draft['email'] ?? null,
                    'phone' => $draft['phone'] ?? '',
                    'reason' => $draft['reason'] ?? null,
                    'type' => $draft['type'],
                    'payment_method' => $paymentMethod,
                    'payment_amount' => $draft['payment_amount'] ?? null,
                    'payment_status' => $payAtHospital ? 'confirmed' : 'pending',
                    'clinic_name' => $draft['clinic_name'] ?? null,
                    'clinic_address' => $draft['clinic_address'] ?? null,
                    'clinic_phone' => $draft['clinic_phone'] ?? null,
                    'clinic_fee' => $draft['clinic_fee'] ?? null,
                    'clinic_notes' => $draft['clinic_notes'] ?? null,
                ]);

                $payment = $payAtHospital
                    ? Payment::create([
                        'appointment_id' => $appointment->id,
                        'payment_method' => $paymentMethod,
                        'reference_number' => 'HOSPITAL-' . $appointment->id,
                        'amount' => $draft['payment_amount'] ?? 0,
                        'status' => 'confirmed',
                        'paid_at' => now(),
                    ])
                    : $fawry->createPendingPayment($appointment, $paymentMethod);

                if (! $payment->exists) {
                    throw new \RuntimeException('Payment record was not saved for appointment #' . $appointment->id);
                }

                return $appointment->fresh(['payment']);
            });

            Log::debug('Booking confirm: after transaction.', $this->bookingLogContext($draft, [
                'appointment_id' => $appointment?->id,
                'payment_id' => $appointment?->payment?->id,
                'payment_method' => $paymentMethod,
            ]));
        } catch (ValidationException $exception) {
            Log::warning('Booking confirm: slot validation failed.', $this->bookingLogContext($draft, [
                'errors' => $exception->errors(),
            ]));

            return redirect()
                ->route('doctors.show', ['doctor' => $doctor->id, 'date' => $draft['date'], 'type' => $draft['type']])
                ->withErrors($exception->errors())
                ->withInput();
        } catch (QueryException $exception) {
            Log::error('Booking confirm: database failure.', $this->bookingLogContext($draft, [
                'sql_state' => $exception->errorInfo[0] ?? null,
                'message' => $exception->getMessage(),
            ]));

            if ($this->isAppointmentSlotConflict($exception)) {
                return redirect()
                    ->route('doctors.show', ['doctor' => $doctor->id, 'date' => $draft['date'], 'type' => $draft['type']])
                    ->withErrors(['time' => 'Slot no longer available'])
                    ->withInput();
            }

            report($exception);

            return back()
                ->withErrors([
                    'payment_method' => 'We could not complete your booking right now. Please review the payment details and try again.',
                ])
                ->withInput();
        } catch (Throwable $exception) {
            Log::error('Appointment confirm failed: '.$exception->getMessage(), $this->bookingLogContext($draft, [
                'exception' => $exception,
            ]));
            report($exception);

            return back()
                ->with('error', 'Payment failed')
                ->withErrors([
                    'payment_method' => 'We could not complete your booking right now. Please review the payment details and try again.',
                ])
                ->withInput();
        } finally {
            if (isset($slotLockName)) {
                $this->releaseSlotLock($slotLockName);
            }
        }

        Session::forget('booking_draft');
        AuditLogger::log('appointment.created', $appointment, [
            'source' => 'patient_booking',
            'payment_method' => $paymentMethod,
        ]);

        if (! $payAtHospital) {
            try {
                $payment = $appointment->payment()->firstOrFail();
                $response = $fawry->initiate($payment);
                $redirectUrl = $fawry->redirectUrl($response);

                if ($redirectUrl) {
                    return redirect()->away($redirectUrl);
                }

                throw new \RuntimeException('Fawry did not return a payment redirect URL.');
            } catch (Throwable $exception) {
                Log::error('Fawry initiation failed: '.$exception->getMessage(), ['exception' => $exception]);
                $appointment->payment()->update(['status' => 'failed']);
                $appointment->update(['payment_status' => 'failed', 'status' => 'Canceled']);
                Session::put('booking_draft', $draft);

                return redirect()
                    ->route('appointments.payment')
                    ->withErrors(['payment_method' => 'Fawry payment could not be started. Please choose another payment method.']);
            }
        }

        return redirect()
            ->route('appointments.invoice', $appointment->id)
            ->with('success', 'Your booking has been completed successfully.');
    }

    public function invoice(Appointment $appointment): View
    {
        $this->ensureAppointmentOwnership($appointment);

        Log::debug('Invoice load: appointment route entered.', [
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'payment_method' => $appointment->payment_method,
            'payment_status' => $appointment->payment_status,
        ]);

        return view('appointments.invoice', $this->patientBookingReceiptData($appointment));
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->ensureAppointmentOwnership($appointment);

        if (! $appointment->canBeManagedByPatient()) {
            return redirect()
                ->route('appointments.invoice', $appointment)
                ->withErrors(['appointment' => 'This appointment can no longer be canceled.']);
        }

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($appointment, $validated): void {
            $appointment->refresh();

            if (! $appointment->canBeManagedByPatient()) {
                throw ValidationException::withMessages([
                    'appointment' => 'This appointment can no longer be canceled.',
                ]);
            }

            $paymentStatus = $this->canceledPaymentStatus($appointment);

            $appointment->update([
                'status' => 'canceled',
                'payment_status' => $paymentStatus,
                'cancellation_reason' => $validated['cancellation_reason'] ?? null,
                'canceled_at' => now(),
            ]);

            $appointment->payments()->update([
                'status' => $paymentStatus,
            ]);
        });

        AuditLogger::log('appointment.canceled', $appointment, [
            'source' => 'patient_invoice',
        ]);

        return redirect()
            ->route('appointments.invoice', $appointment)
            ->with('success', 'Appointment canceled successfully');
    }

    public function rescheduleSlots(Request $request, Appointment $appointment): JsonResponse
    {
        $this->ensureAppointmentOwnership($appointment);

        if (! $appointment->canBeManagedByPatient()) {
            return response()->json([
                'message' => 'This appointment can no longer be rescheduled.',
                'slots' => [],
                'available_count' => 0,
            ], 422);
        }

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $slots = $this->availableSlotsForAppointment($appointment, $validated['date']);

        return response()->json([
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'date' => $validated['date'],
            'type' => PrivateClinicBookingSupport::normalizeType($appointment->type),
            'available_count' => count($slots),
            'slots' => $slots,
        ]);
    }

    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->ensureAppointmentOwnership($appointment);

        if (! $appointment->canBeManagedByPatient()) {
            return redirect()
                ->route('appointments.invoice', $appointment)
                ->withErrors(['appointment' => 'This appointment can no longer be rescheduled.']);
        }

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        $appointment->loadMissing(['doctor.privateClinic']);
        $doctor = $appointment->doctor;
        abort_unless($doctor instanceof Doctor, 404);

        $type = PrivateClinicBookingSupport::normalizeType($appointment->type);
        $normalizedTime = AppointmentSecurity::normalizeTime($validated['time']);

        if (! PrivateClinicBookingSupport::isDateAvailable($doctor, $type, $validated['date'])) {
            return back()->withErrors(['date' => 'The selected date is not available for this appointment type.']);
        }

        try {
            $slotLockName = $this->acquireSlotLock($doctor, $type, $validated['date'], $normalizedTime);
            $this->ensureGeneratedSlotAvailable($doctor, $type, $validated['date'], $normalizedTime, $appointment->id);

            DB::transaction(function () use ($appointment, $doctor, $type, $validated, $normalizedTime): void {
                $appointment->refresh();

                if (! $appointment->canBeManagedByPatient()) {
                    throw ValidationException::withMessages([
                        'appointment' => 'This appointment can no longer be rescheduled.',
                    ]);
                }

                $this->lockCurrentSlotRows($doctor, $type, $validated['date'], $normalizedTime, $appointment->id);
                $this->ensureGeneratedSlotAvailable($doctor, $type, $validated['date'], $normalizedTime, $appointment->id);

                $appointment->update([
                    'date' => $validated['date'],
                    'time' => $normalizedTime,
                ]);
            });
        } catch (ValidationException $exception) {
            return redirect()
                ->route('appointments.invoice', $appointment)
                ->withErrors($exception->errors());
        } finally {
            if (isset($slotLockName)) {
                $this->releaseSlotLock($slotLockName);
            }
        }

        AuditLogger::log('appointment.rescheduled', $appointment, [
            'source' => 'patient_invoice',
            'date' => $validated['date'],
            'time' => $normalizedTime,
        ]);

        return redirect()
            ->route('appointments.invoice', $appointment)
            ->with('success', 'Appointment rescheduled successfully');
    }

    public function rate(Appointment $appointment): View|RedirectResponse
    {
        $this->ensureAppointmentOwnership($appointment);

        if ($appointment->rating()->exists()) {
            return redirect()->route('appointments.invoice', $appointment->id);
        }

        if (! $appointment->canReceiveDoctorRating()) {
            return redirect()
                ->route('appointments.invoice', $appointment->id)
                ->withErrors(['rating' => 'Doctor ratings are available after your appointment is completed by the doctor.']);
        }

        $appointment->load('doctor.department');

        return view('appointments.rating', compact('appointment'));
    }

    public function start()
    {
        return redirect()->to($this->bookingStartUrl());
    }

    private function bookingStartUrl(): string
    {
        $departmentId = Session::get('booking_draft.department_id');

        if ($departmentId && Department::query()->whereKey($departmentId)->exists()) {
            return route('specialties.doctors', ['department' => $departmentId]);
        }

        return route('home') . '#departments-section';
    }

    private function ensureGeneratedSlotAvailable(
        Doctor $doctor,
        string $bookingType,
        string $date,
        string $time,
        ?int $ignoreAppointmentId = null
    ): void
    {
        $normalizedTime = AppointmentSecurity::normalizeTime($time);
        $scheduleType = $this->scheduleTypeForBookingType($bookingType);
        $slots = app(SlotGenerationService::class)->generate($doctor->id, $scheduleType, $date, null, $ignoreAppointmentId);

        foreach ($slots as $slot) {
            if (($slot['date'] ?? null) === $date && AppointmentSecurity::normalizeTime($slot['start_time'] ?? '') === $normalizedTime) {
                return;
            }
        }

        throw ValidationException::withMessages([
            'time' => 'Slot no longer available',
        ]);
    }

    private function lockCurrentSlotRows(
        Doctor $doctor,
        string $bookingType,
        string $date,
        string $time,
        ?int $ignoreAppointmentId = null
    ): void
    {
        $normalizedTime = AppointmentSecurity::normalizeTime($time);

        $scheduleType = $this->scheduleTypeForBookingType($bookingType);
        $duration = $this->appointmentDurationMinutes($doctor->id, $scheduleType);
        $timezone = config('app.timezone', 'Africa/Cairo');
        $slotStart = CarbonImmutable::parse($date . ' ' . $normalizedTime, $timezone);
        $slotEnd = $slotStart->addMinutes($duration);

        $appointmentOverlap = AppointmentSecurity::blockingAppointments(
            $doctor->id,
            $date,
            $date,
            $ignoreAppointmentId
        )
            ->lockForUpdate()
            ->get(['date', 'time'])
            ->contains(function (Appointment $appointment) use ($date, $duration, $timezone, $slotStart, $slotEnd): bool {
                $appointmentTime = AppointmentSecurity::normalizeTime((string) $appointment->time);

                if ($appointmentTime === '') {
                    return true;
                }

                $appointmentStart = CarbonImmutable::parse($date . ' ' . $appointmentTime, $timezone);
                $appointmentEnd = $appointmentStart->addMinutes($duration);

                return $slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
            });

        if ($appointmentOverlap) {
            throw ValidationException::withMessages([
                'time' => 'Slot no longer available',
            ]);
        }

        $blockedExists = BlockedTime::query()
            ->where('doctor_id', $doctor->id)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->where('starts_at', '<', $slotEnd)
            ->where('ends_at', '>', $slotStart)
            ->lockForUpdate()
            ->exists();

        if ($blockedExists) {
            throw ValidationException::withMessages([
                'time' => 'Slot no longer available',
            ]);
        }
    }

    private function appointmentDurationMinutes(int $doctorId, string $scheduleType): int
    {
        return (int) DoctorAvailability::query()
            ->where('doctor_id', $doctorId)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->latest('id')
            ->value('appointment_duration_minutes') ?: 15;
    }

    private function scheduleTypeForBookingType(string $bookingType): string
    {
        return $bookingType === 'private' ? 'private_clinic' : 'hospital';
    }

    private function isAppointmentSlotConflict(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $message = $exception->getMessage();

        return $sqlState === '23000'
            && str_contains($message, 'appointments_doctor_date_time_type_unique');
    }

    private function acquireSlotLock(Doctor $doctor, string $bookingType, string $date, string $time): ?string
    {
        if (DB::getDriverName() !== 'mysql') {
            return null;
        }

        $lockName = implode(':', [
            'booking-slot',
            $doctor->id,
            $date,
            AppointmentSecurity::normalizeTime($time),
            $bookingType,
        ]);

        $result = DB::selectOne('SELECT GET_LOCK(?, 10) AS acquired', [$lockName]);

        if ((int) ($result->acquired ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'time' => 'Slot is currently being booked. Please try again.',
            ]);
        }

        return $lockName;
    }

    private function releaseSlotLock(?string $lockName): void
    {
        if ($lockName === null || DB::getDriverName() !== 'mysql') {
            return;
        }

        try {
            DB::selectOne('SELECT RELEASE_LOCK(?) AS released', [$lockName]);
        } catch (Throwable $exception) {
            Log::warning('Booking confirm: failed to release slot lock.', [
                'slot_lock' => $lockName,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param array<string, mixed> $draft
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function bookingLogContext(array $draft, array $extra = []): array
    {
        return $extra + [
            'doctor_id' => $draft['doctor_id'] ?? null,
            'date' => $draft['date'] ?? null,
            'time' => $draft['time'] ?? null,
            'type' => $draft['type'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function patientBookingReceiptData(Appointment $appointment): array
    {
        $appointment->loadMissing(['doctor.department', 'department', 'websiteRating', 'rating', 'payment']);

        Log::debug('Invoice load: relationships loaded.', [
            'appointment_id' => $appointment->id,
            'has_doctor' => (bool) $appointment->doctor,
            'has_patient' => (bool) $appointment->patient_id,
            'has_payment' => (bool) $appointment->payment,
            'has_department' => (bool) ($appointment->department ?? $appointment->doctor?->department),
        ]);

        $dailyQueueIds = Appointment::query()
            ->where('doctor_id', $appointment->doctor_id)
            ->whereDate('date', $appointment->date)
            ->whereIn('status', ['Confirmed', 'Pending'])
            ->orderBy('time')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $queuePosition = $dailyQueueIds->search($appointment->id);
        $patientNumber = $queuePosition === false ? 1 : $queuePosition + 1;
        $waitingPatients = max($patientNumber - 1, 0);
        $expectedTimeMinutes = max($waitingPatients * 15, 15);

        return [
            'appointment' => $appointment,
            'patientNumber' => $patientNumber,
            'waitingPatients' => $waitingPatients,
            'expectedTimeMinutes' => $expectedTimeMinutes,
        ];
    }

    /**
     * @return array<int, array{time:string,end_time:string,label:string}>
     */
    private function availableSlotsForAppointment(Appointment $appointment, string $date): array
    {
        $appointment->loadMissing('doctor.privateClinic');
        $doctor = $appointment->doctor;

        if (! $doctor instanceof Doctor) {
            return [];
        }

        $type = PrivateClinicBookingSupport::normalizeType($appointment->type);

        if (! PrivateClinicBookingSupport::isDateAvailable($doctor, $type, $date)) {
            return [];
        }

        $scheduleType = $this->scheduleTypeForBookingType($type);

        return collect(app(SlotGenerationService::class)->generate($doctor->id, $scheduleType, $date, null, $appointment->id))
            ->map(fn (array $slot) => [
                'time' => AppointmentSecurity::normalizeTime((string) ($slot['start_time'] ?? '')),
                'end_time' => AppointmentSecurity::normalizeTime((string) ($slot['end_time'] ?? '')),
                'label' => 'Available',
            ])
            ->filter(fn (array $slot) => $slot['time'] !== '')
            ->unique('time')
            ->sortBy('time')
            ->values()
            ->all();
    }

    private function canceledPaymentStatus(Appointment $appointment): string
    {
        $appointment->loadMissing('payment');
        $paymentStatus = strtolower((string) ($appointment->payment?->status ?? $appointment->payment_status ?? 'pending'));

        return in_array($paymentStatus, ['confirmed', 'paid', 'receipt_submitted'], true)
            ? 'pending_refund'
            : 'canceled';
    }

    private function ensureAppointmentOwnership(Appointment $appointment): void
    {
        abort_unless(
            Auth::guard('patient')->check()
                && (int) $appointment->patient_id === (int) Auth::guard('patient')->id(),
            403
        );
    }
}
