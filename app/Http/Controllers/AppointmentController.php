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
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class AppointmentController extends Controller
{
    public function create(Doctor $doctor, string $time): View
    {
        $doctor->loadMissing(['department', 'privateClinic']);
        $hasPrivateClinic = PrivateClinicBookingSupport::hasPrivateClinic($doctor);
        $selectedDate = CarbonImmutable::parse(
            request('date', now()->toDateString()),
            config('app.timezone', 'Africa/Cairo')
        )->toDateString();
        $selectedType = PrivateClinicBookingSupport::normalizeType(request('type'));

        if ($selectedType === 'private' && !$hasPrivateClinic) {
            $selectedType = 'hospital';
        }

        $estimatedAmount = PrivateClinicBookingSupport::calculateAmount($doctor, $selectedType);
        $normalizedTime = AppointmentSecurity::normalizeTime($time);
        $slotStillAvailable = true;

        Session::forget(['booking_selection', 'booking_draft']);

        $selection = [
            'token' => (string) Str::uuid(),
            'doctor_id' => $doctor->id,
            'type' => $selectedType,
            'date' => $selectedDate,
            'time' => $normalizedTime,
        ];

        Log::info('Booking flow debug: new slot selected and stale draft cleared.', [
            'doctor_id' => $doctor->id,
            'selected_type' => $selection['type'],
            'selected_date' => $selection['date'],
            'selected_time' => $selection['time'],
            'selection_token_present' => true,
            'previous_draft_cleared' => true,
        ]);

        try {
            $this->ensureGeneratedSlotAvailable($doctor, $selectedType, $selectedDate, $normalizedTime);
        } catch (ValidationException) {
            $slotStillAvailable = false;
        }

        if ($slotStillAvailable) {
            Session::put('booking_selection', $selection);
        }

        return view('appointments.create', compact(
            'doctor',
            'time',
            'normalizedTime',
            'selectedDate',
            'estimatedAmount',
            'selectedType',
            'hasPrivateClinic',
            'slotStillAvailable',
            'selection'
        ));
    }

    public function review(AppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $doctor = Doctor::with('privateClinic')->findOrFail($data['doctor_id']);
        $type = PrivateClinicBookingSupport::normalizeType($data['type']);
        $normalizedTime = AppointmentSecurity::normalizeTime((string) $data['time']);
        $selectedDate = CarbonImmutable::parse(
            $data['date'] ?? now()->toDateString(),
            config('app.timezone', 'Africa/Cairo')
        )->toDateString();
        $selection = Session::get('booking_selection');

        Log::info('Booking flow debug: review payload compared with selected slot.', [
            'doctor_id' => $doctor->id,
            'selected_type' => $selection['type'] ?? null,
            'selected_date' => $selection['date'] ?? null,
            'selected_time' => $selection['time'] ?? null,
            'posted_type' => $type,
            'posted_date' => $selectedDate,
            'posted_time' => $normalizedTime,
            'selection_token_matches' => isset($selection['token'])
                && hash_equals((string) $selection['token'], (string) ($data['booking_token'] ?? '')),
        ]);

        if (! $this->selectionMatchesReview($selection, $data['booking_token'] ?? null, $doctor->id, $type, $selectedDate, $normalizedTime)) {
            Session::forget(['booking_selection', 'booking_draft']);

            return redirect()
                ->route('doctors.show', ['doctor' => $doctor->id, 'date' => $selectedDate, 'type' => $type])
                ->withErrors(['time' => 'Your selected slot changed or expired. Please select it again.']);
        }

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
            'token' => (string) ($selection['token'] ?? Str::uuid()),
        ];

        $draft['payment_amount'] = PrivateClinicBookingSupport::calculateAmount($doctor, $draft['type']);
        $draft += PrivateClinicBookingSupport::clinicSnapshot($doctor, $draft['type']);

        Session::forget('booking_draft');
        Session::put('booking_draft', $draft);
        Session::forget('booking_selection');

        Log::info('Booking flow debug: current draft replaced from reviewed selection.', [
            'doctor_id' => $draft['doctor_id'],
            'draft_type' => $draft['type'],
            'draft_date' => $draft['date'],
            'draft_time' => $draft['time'],
            'draft_token_present' => true,
            'old_draft_replaced' => true,
        ]);

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

        Log::info('Booking flow debug: payment page loaded current draft.', [
            'doctor_id' => $draft['doctor_id'],
            'draft_type' => $draft['type'],
            'draft_date' => $draft['date'],
            'draft_time' => $draft['time'],
            'draft_token_present' => isset($draft['token']),
        ]);

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

        $draft = $this->normalizeBookingDraft($draft);

        if (! $this->confirmTokenMatchesDraft($request->validated('booking_token'), $draft)) {
            Session::forget('booking_draft');

            return redirect()->to($this->bookingStartUrl())->withErrors([
                'booking' => 'This payment page is stale. Please select the appointment slot again.',
            ]);
        }

        Session::put('booking_draft', $draft);
        $doctor = Doctor::with('privateClinic')->findOrFail($draft['doctor_id']);

        Log::info('Booking flow debug: confirm uses normalized current draft.', [
            'doctor_id' => $draft['doctor_id'],
            'draft_type' => $draft['type'],
            'draft_date' => $draft['date'],
            'draft_time' => $draft['time'],
            'draft_token_matches' => $this->confirmTokenMatchesDraft($request->validated('booking_token'), $draft),
        ]);

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
                $this->logSlotRejection(
                    'database_unique_constraint',
                    $doctor,
                    $draft['type'],
                    $draft['date'],
                    $draft['time'],
                    null,
                    [
                        'sql_state' => $exception->errorInfo[0] ?? null,
                        'database_message' => $exception->getMessage(),
                    ]
                );

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

        $this->logSlotRejection(
            'generated_slot_missing',
            $doctor,
            $bookingType,
            $date,
            $time,
            $ignoreAppointmentId,
            ['generated_slots_from_validation' => $slots]
        );

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

        $appointmentConflicts = AppointmentSecurity::blockingAppointments(
            $doctor->id,
            $date,
            $date,
            $ignoreAppointmentId
        )
            ->lockForUpdate()
            ->get(['id', 'date', 'time', 'status', 'type']);

        $appointmentOverlap = $appointmentConflicts
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
            $this->logSlotRejection(
                'appointment_overlap',
                $doctor,
                $bookingType,
                $date,
                $time,
                $ignoreAppointmentId,
                ['locked_appointment_conflicts' => $appointmentConflicts->toArray()]
            );

            throw ValidationException::withMessages([
                'time' => 'Slot no longer available',
            ]);
        }

        $blockedConflicts = BlockedTime::query()
            ->where('doctor_id', $doctor->id)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->where('starts_at', '<', $slotEnd)
            ->where('ends_at', '>', $slotStart)
            ->lockForUpdate()
            ->get(['id', 'starts_at', 'ends_at', 'reason', 'source', 'appointment_id']);

        if ($blockedConflicts->isNotEmpty()) {
            $this->logSlotRejection(
                'blocked_time_overlap',
                $doctor,
                $bookingType,
                $date,
                $time,
                $ignoreAppointmentId,
                ['locked_blocked_conflicts' => $blockedConflicts->toArray()]
            );

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

    /**
     * @param array<string, mixed>|null $selection
     */
    private function selectionMatchesReview(
        ?array $selection,
        ?string $submittedToken,
        int $doctorId,
        string $type,
        string $date,
        string $time
    ): bool {
        if ($selection === null) {
            return $submittedToken === null;
        }

        $tokenMatches = $submittedToken === null
            || (isset($selection['token']) && hash_equals((string) $selection['token'], $submittedToken));

        return $tokenMatches
            && (int) ($selection['doctor_id'] ?? 0) === $doctorId
            && (string) ($selection['type'] ?? '') === $type
            && (string) ($selection['date'] ?? '') === $date
            && (string) ($selection['time'] ?? '') === $time;
    }

    /**
     * @param array<string, mixed> $draft
     * @return array<string, mixed>
     */
    private function normalizeBookingDraft(array $draft): array
    {
        $draft['doctor_id'] = (int) ($draft['doctor_id'] ?? 0);
        $draft['type'] = PrivateClinicBookingSupport::normalizeType($draft['type'] ?? null);
        $draft['date'] = CarbonImmutable::parse(
            $draft['date'] ?? now()->toDateString(),
            config('app.timezone', 'Africa/Cairo')
        )->toDateString();
        $draft['time'] = AppointmentSecurity::normalizeTime((string) ($draft['time'] ?? ''));

        return $draft;
    }

    /**
     * @param array<string, mixed> $draft
     */
    private function confirmTokenMatchesDraft(?string $submittedToken, array $draft): bool
    {
        $draftToken = $draft['token'] ?? null;

        if ($submittedToken === null) {
            return true;
        }

        if ($draftToken === null) {
            return false;
        }

        return hash_equals((string) $draftToken, $submittedToken);
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function logSlotRejection(
        string $reason,
        Doctor $doctor,
        string $bookingType,
        string $date,
        string $time,
        ?int $ignoreAppointmentId = null,
        array $extra = []
    ): void {
        try {
            Log::warning('Booking slot rejected: Slot no longer available.', $extra + $this->slotDiagnostics(
                $doctor,
                $bookingType,
                $date,
                $time,
                $ignoreAppointmentId
            ) + [
                'rejection_reason' => $reason,
                'route' => request()->route()?->getName(),
                'request_path' => request()->path(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Booking slot rejected: diagnostics failed.', [
                'rejection_reason' => $reason,
                'doctor_id' => $doctor->id,
                'booking_type' => $bookingType,
                'date' => $date,
                'time' => $time,
                'normalized_time' => AppointmentSecurity::normalizeTime($time),
                'schedule_type' => $this->scheduleTypeForBookingType($bookingType),
                'diagnostic_error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function slotDiagnostics(
        Doctor $doctor,
        string $bookingType,
        string $date,
        string $time,
        ?int $ignoreAppointmentId = null
    ): array {
        $normalizedTime = AppointmentSecurity::normalizeTime($time);
        $scheduleType = $this->scheduleTypeForBookingType($bookingType);
        $availability = DoctorAvailability::query()
            ->where('doctor_id', $doctor->id)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->latest('id')
            ->first();
        $timezone = $availability?->timezone ?: config('app.timezone', 'Africa/Cairo');
        $duration = (int) ($availability?->appointment_duration_minutes ?: 15);
        $slotStart = CarbonImmutable::parse($date . ' ' . $normalizedTime, $timezone);
        $slotEnd = $slotStart->addMinutes($duration);
        $generatedSlots = app(SlotGenerationService::class)
            ->generate($doctor->id, $scheduleType, $date, null, $ignoreAppointmentId);

        $appointmentConflicts = AppointmentSecurity::blockingAppointments(
            $doctor->id,
            $date,
            $date,
            $ignoreAppointmentId
        )
            ->get(['id', 'date', 'time', 'status', 'type'])
            ->map(function (Appointment $appointment) use ($date, $duration, $timezone, $slotStart, $slotEnd): array {
                $appointmentTime = AppointmentSecurity::normalizeTime((string) $appointment->time);
                $overlaps = true;

                if ($appointmentTime !== '') {
                    $appointmentStart = CarbonImmutable::parse($date . ' ' . $appointmentTime, $timezone);
                    $appointmentEnd = $appointmentStart->addMinutes($duration);
                    $overlaps = $slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
                }

                return [
                    'id' => $appointment->id,
                    'date' => (string) $appointment->date,
                    'time' => (string) $appointment->time,
                    'normalized_time' => $appointmentTime,
                    'status' => $appointment->status,
                    'type' => $appointment->type,
                    'overlaps_selected_slot' => $overlaps,
                ];
            })
            ->values()
            ->all();

        $blockedConflicts = BlockedTime::query()
            ->where('doctor_id', $doctor->id)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->where('starts_at', '<', $slotEnd)
            ->where('ends_at', '>', $slotStart)
            ->get(['id', 'starts_at', 'ends_at', 'reason', 'source', 'appointment_id'])
            ->toArray();

        return [
            'doctor_id' => $doctor->id,
            'booking_type' => $bookingType,
            'date' => $date,
            'time' => $time,
            'normalized_time' => $normalizedTime,
            'schedule_type' => $scheduleType,
            'availability_id' => $availability?->id,
            'availability_timezone' => $timezone,
            'appointment_duration_minutes' => $duration,
            'generated_slots' => collect($generatedSlots)
                ->map(fn (array $slot) => [
                    'date' => $slot['date'] ?? null,
                    'time' => AppointmentSecurity::normalizeTime((string) ($slot['start_time'] ?? '')),
                    'end_time' => AppointmentSecurity::normalizeTime((string) ($slot['end_time'] ?? '')),
                ])
                ->values()
                ->all(),
            'appointment_conflicts' => $appointmentConflicts,
            'blocked_conflicts' => $blockedConflicts,
        ];
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
