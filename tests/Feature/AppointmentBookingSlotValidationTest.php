<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Admin;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Patient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AppointmentBookingSlotValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $env = $this->readEnv();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $env['DB_HOST'] ?? '127.0.0.1',
            'database.connections.mysql.port' => $env['DB_PORT'] ?? '3306',
            'database.connections.mysql.database' => $env['DB_DATABASE'] ?? 'cura_axis',
            'database.connections.mysql.username' => $env['DB_USERNAME'] ?? 'root',
            'database.connections.mysql.password' => $env['DB_PASSWORD'] ?? '',
        ]);

        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::beginTransaction();
        Carbon::setTestNow(Carbon::parse('2026-05-04 08:00:00', 'Africa/Cairo'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        parent::tearDown();
    }

    public function test_booking_valid_generated_slot_succeeds(): void
    {
        [$doctor] = $this->doctorWithAvailability();
        $patient = $this->patient();

        $this->actingAs($patient, 'patient')
            ->get(route('appointments.create', [
            'doctor' => $doctor,
            'time' => '09:00:00',
            'date' => '2026-05-04',
            'type' => 'hospital',
            ]))
            ->assertOk()
            ->assertSee('name="doctor_id" value="'.$doctor->id.'"', false)
            ->assertSee('name="time" value="09:00"', false)
            ->assertSee('name="date" value="2026-05-04"', false)
            ->assertSee('name="type" value="hospital"', false)
            ->assertSee('action="/appointments/review" method="POST"', false)
            ->assertSee('value="hospital" selected', false);

        $this->getJson(route('doctors.booked-slots', [
            'doctor' => $doctor,
            'date' => '2026-05-04',
            'type' => 'hospital',
        ]))
            ->assertOk()
            ->assertJsonFragment([
                'time' => '09:00',
                'available' => true,
                'label' => 'Available',
            ]);

        $this->post(route('appointments.review'), $this->reviewPayload($doctor, '09:00'))
            ->assertRedirect(route('appointments.payment'));

        $booking = $this->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital']);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'date' => '2026-05-04',
            'time' => '09:00',
            'status' => 'Confirmed',
            'payment_method' => 'pay_at_hospital',
            'payment_status' => 'confirmed',
        ]);

        $appointment = Appointment::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', '2026-05-04')
            ->where('time', '09:00')
            ->firstOrFail();

        $booking->assertRedirect(route('appointments.invoice', $appointment));

        $invoice = $this->get(route('appointments.invoice', $appointment));
        $invoice->assertOk();
        $invoice->assertSee('Appointment Confirmed!', false);
        $invoice->assertSee('Rate Your Experience', false);
        $invoice->assertDontSee('Manage Appointment', false);
        $invoice->assertDontSee('Cancel Appointment', false);
        $invoice->assertDontSee('Reschedule Appointment', false);
        $invoice->assertSee('Your feedback helps us improve', false);
        $invoice->assertDontSee('Rate Your Doctor', false);
        $invoice->assertDontSee('payment_status', false);
        $invoice->assertDontSee('receipt status', false);

        $optionalRating = $this->post(route('site.ratings.store', $appointment), []);
        $optionalRating->assertRedirect(route('appointments.invoice', $appointment));
        $optionalRating->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('website_ratings', [
            'appointment_id' => $appointment->id,
        ]);

        $rating = $this->post(route('site.ratings.store', $appointment), [
            'feedback' => 'Smooth booking experience',
        ]);

        $rating->assertRedirect(route('appointments.invoice', $appointment));
        $this->assertDatabaseHas('website_ratings', [
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'rating' => null,
            'comment' => 'Smooth booking experience',
        ]);

        $duplicate = $this->post(route('site.ratings.store', $appointment), [
            'rating' => 4,
            'feedback' => 'Second rating',
        ]);

        $duplicate->assertRedirect(route('appointments.invoice', $appointment));
        $duplicate->assertSessionHas('error', 'Already rated');
    }

    public function test_booking_invalid_generated_slot_fails(): void
    {
        [$doctor] = $this->doctorWithAvailability(scheduleEnd: '09:30');
        $patient = $this->patient();
        Log::spy();

        $this->actingAs($patient, 'patient')
            ->post(route('appointments.review'), $this->reviewPayload($doctor, '10:00'))
            ->assertSessionHasErrors(['time' => 'Slot no longer available']);

        Log::shouldHaveReceived('warning')
            ->withArgs(function (string $message, array $context) use ($doctor): bool {
                return $message === 'Booking slot rejected: Slot no longer available.'
                    && $context['rejection_reason'] === 'generated_slot_missing'
                    && $context['doctor_id'] === $doctor->id
                    && $context['booking_type'] === 'hospital'
                    && $context['date'] === '2026-05-04'
                    && $context['time'] === '10:00'
                    && $context['normalized_time'] === '10:00'
                    && $context['schedule_type'] === 'hospital'
                    && is_array($context['generated_slots'])
                    && is_array($context['appointment_conflicts'])
                    && is_array($context['blocked_conflicts']);
            })
            ->once();

        $this->assertDatabaseMissing('appointments', [
            'doctor_id' => $doctor->id,
            'date' => '2026-05-04',
            'time' => '10:00',
        ]);
    }

    public function test_new_slot_selection_clears_old_draft_and_review_replaces_it(): void
    {
        [$doctor] = $this->doctorWithAvailability(scheduleEnd: '10:30', breakBetween: 0);
        $patient = $this->patient();

        $response = $this->actingAs($patient, 'patient')
            ->withSession(['booking_draft' => $this->bookingDraft($doctor, '09:30')])
            ->get(route('appointments.create', [
                'doctor' => $doctor,
                'time' => '09:00:00',
                'date' => '2026-05-04',
                'type' => 'hospital',
            ]));

        $response->assertOk()
            ->assertSessionMissing('booking_draft')
            ->assertSessionHas('booking_selection.doctor_id', $doctor->id)
            ->assertSessionHas('booking_selection.type', 'hospital')
            ->assertSessionHas('booking_selection.date', '2026-05-04')
            ->assertSessionHas('booking_selection.time', '09:00');

        $token = (string) session('booking_selection.token');

        $this->post(route('appointments.review'), $this->reviewPayload($doctor, '09:00') + [
            'booking_token' => $token,
        ])
            ->assertRedirect(route('appointments.payment'))
            ->assertSessionMissing('booking_selection')
            ->assertSessionHas('booking_draft.doctor_id', $doctor->id)
            ->assertSessionHas('booking_draft.type', 'hospital')
            ->assertSessionHas('booking_draft.date', '2026-05-04')
            ->assertSessionHas('booking_draft.time', '09:00')
            ->assertSessionHas('booking_draft.token', $token);
    }

    public function test_stale_payment_page_cannot_confirm_replaced_draft(): void
    {
        [$doctor] = $this->doctorWithAvailability(scheduleEnd: '10:30', breakBetween: 0);
        $patient = $this->patient();
        $this->actingAs($patient, 'patient');

        $this->get(route('appointments.create', [
            'doctor' => $doctor,
            'time' => '09:00',
            'date' => '2026-05-04',
            'type' => 'hospital',
        ]))->assertOk();
        $oldToken = (string) session('booking_selection.token');

        $this->post(route('appointments.review'), $this->reviewPayload($doctor, '09:00') + [
            'booking_token' => $oldToken,
        ])->assertRedirect(route('appointments.payment'));

        $this->get(route('appointments.create', [
            'doctor' => $doctor,
            'time' => '09:30',
            'date' => '2026-05-04',
            'type' => 'hospital',
        ]))->assertOk();
        $newToken = (string) session('booking_selection.token');

        $this->post(route('appointments.review'), $this->reviewPayload($doctor, '09:30') + [
            'booking_token' => $newToken,
        ])->assertRedirect(route('appointments.payment'));

        $this->post(route('appointments.confirm'), [
            'payment_method' => 'pay_at_hospital',
            'booking_token' => $oldToken,
        ])
            ->assertRedirect()
            ->assertSessionHasErrors(['booking'])
            ->assertSessionMissing('booking_draft');

        $this->assertDatabaseMissing('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'date' => '2026-05-04',
            'time' => '09:30',
        ]);
    }

    public function test_canceled_appointment_is_shown_available_and_can_be_booked(): void
    {
        [$doctor] = $this->doctorWithAvailability();
        $patient = $this->patient();

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => '2026-05-04',
            'time' => '09:00:00',
            'status' => ' Cancelled ',
            'first_name' => 'Canceled',
            'last_name' => 'Patient',
            'phone' => '01000000000',
            'type' => 'hospital',
        ]);

        $this->getJson(route('doctors.booked-slots', [
            'doctor' => $doctor,
            'date' => '2026-05-04',
            'type' => 'hospital',
        ]))
            ->assertOk()
            ->assertJsonFragment([
                'time' => '09:00',
                'available' => true,
                'label' => 'Available',
            ]);

        $this->actingAs($patient, 'patient')
            ->withSession(['booking_draft' => $this->bookingDraft($doctor, '09:00')])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'date' => '2026-05-04',
            'time' => '09:00',
            'status' => 'Confirmed',
        ]);
    }

    public function test_concurrent_booking_conflict_fails_gracefully(): void
    {
        [$doctor] = $this->doctorWithAvailability();
        $firstPatient = $this->patient();
        $secondPatient = $this->patient();
        $draft = $this->bookingDraft($doctor, '09:00');

        $this->actingAs($firstPatient, 'patient')
            ->withSession(['booking_draft' => $draft])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertRedirect();

        $this->actingAs($secondPatient, 'patient')
            ->withSession(['booking_draft' => $draft])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertSessionHasErrors(['time' => 'Slot no longer available']);

        $this->assertSame(1, Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', '2026-05-04')
            ->where('time', '09:00')
            ->count());
    }

    public function test_patient_can_cancel_own_confirmed_appointment_and_release_slot(): void
    {
        [$doctor] = $this->doctorWithAvailability();
        $patient = $this->patient();

        $this->actingAs($patient, 'patient')
            ->withSession(['booking_draft' => $this->bookingDraft($doctor, '09:00')])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertRedirect();

        $appointment = Appointment::query()
            ->with('payment')
            ->where('patient_id', $patient->id)
            ->where('time', '09:00')
            ->firstOrFail();

        $this->post(route('appointments.cancel', $appointment), [
            'cancellation_reason' => 'Patient schedule changed',
        ])->assertRedirect(route('appointments.invoice', $appointment))
            ->assertSessionHas('success', 'Appointment canceled successfully');

        $appointment->refresh();
        $this->assertSame('canceled', $appointment->status);
        $this->assertSame('pending_refund', $appointment->payment_status);
        $this->assertSame('Patient schedule changed', $appointment->cancellation_reason);
        $this->assertNotNull($appointment->canceled_at);
        $this->assertSame('pending_refund', $appointment->payment()->first()?->status);

        $secondPatient = $this->patient();
        $this->actingAs($secondPatient, 'patient')
            ->withSession(['booking_draft' => $this->bookingDraft($doctor, '09:00')])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $secondPatient->id,
            'doctor_id' => $doctor->id,
            'date' => '2026-05-04',
            'time' => '09:00',
            'status' => 'Confirmed',
        ]);
    }

    public function test_patient_can_reschedule_paid_appointment_without_losing_payment(): void
    {
        [$doctor] = $this->doctorWithAvailability(scheduleEnd: '10:30', breakBetween: 0);
        $patient = $this->patient();

        $this->actingAs($patient, 'patient')
            ->withSession(['booking_draft' => $this->bookingDraft($doctor, '09:00')])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertRedirect();

        $appointment = Appointment::query()
            ->with('payment')
            ->where('patient_id', $patient->id)
            ->where('time', '09:00')
            ->firstOrFail();
        $paymentId = $appointment->payment->id;

        $appointment->update(['payment_status' => 'confirmed']);
        $appointment->payment()->update(['status' => 'confirmed']);

        $slots = $this->getJson(route('appointments.reschedule-slots', [
            'appointment' => $appointment,
            'date' => '2026-05-04',
        ]));

        $slots->assertOk()
            ->assertJsonFragment(['time' => '09:30']);

        $this->post(route('appointments.reschedule', $appointment), [
            'date' => '2026-05-04',
            'time' => '09:30',
        ])->assertRedirect(route('appointments.invoice', $appointment))
            ->assertSessionHas('success', 'Appointment rescheduled successfully');

        $appointment->refresh();
        $this->assertSame('2026-05-04', (string) $appointment->date);
        $this->assertSame('09:30', substr((string) $appointment->time, 0, 5));
        $this->assertSame('confirmed', $appointment->payment_status);
        $this->assertSame($paymentId, $appointment->payment()->first()?->id);
        $this->assertSame('confirmed', $appointment->payment()->first()?->status);
    }

    public function test_patient_cannot_manage_other_patient_or_completed_appointments(): void
    {
        [$doctor] = $this->doctorWithAvailability(scheduleEnd: '10:30', breakBetween: 0);
        $owner = $this->patient();
        $intruder = $this->patient();

        $this->actingAs($owner, 'patient')
            ->withSession(['booking_draft' => $this->bookingDraft($doctor, '09:00')])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertRedirect();

        $appointment = Appointment::query()
            ->where('patient_id', $owner->id)
            ->where('time', '09:00')
            ->firstOrFail();

        $this->actingAs($intruder, 'patient')
            ->post(route('appointments.cancel', $appointment))
            ->assertForbidden();

        $this->actingAs($owner, 'patient');
        $appointment->update(['status' => 'Completed']);

        $this->post(route('appointments.cancel', $appointment))
            ->assertSessionHasErrors(['appointment']);

        $this->post(route('appointments.reschedule', $appointment), [
            'date' => '2026-05-04',
            'time' => '09:30',
        ])->assertSessionHasErrors(['appointment']);

        $appointment->refresh();
        $this->assertSame('Completed', $appointment->status);
        $this->assertSame('09:00', substr((string) $appointment->time, 0, 5));
    }

    public function test_invoice_access_uses_patient_guard_and_management_ui_lives_on_profile(): void
    {
        [$doctor] = $this->doctorWithAvailability(scheduleEnd: '10:30', breakBetween: 0);
        $patient = $this->patient();
        $admin = Admin::query()->firstOrCreate(
            ['email' => 'invoice-guard-admin@example.com'],
            ['password' => bcrypt('password')]
        );

        $this->actingAs($patient, 'patient')
            ->withSession(['booking_draft' => $this->bookingDraft($doctor, '09:00')])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertRedirect();

        $appointment = Appointment::query()
            ->where('patient_id', $patient->id)
            ->where('time', '09:00')
            ->firstOrFail();

        $invoice = $this->actingAs($admin, 'admin')
            ->actingAs($patient, 'patient')
            ->get(route('appointments.invoice', $appointment));

        $invoice->assertOk();
        $invoice->assertSee('Appointment Confirmed!', false);
        $invoice->assertDontSee('Manage Appointment', false);
        $invoice->assertDontSee('Cancel Appointment', false);
        $invoice->assertDontSee('Reschedule Appointment', false);

        $profile = $this->get(route('profile.show'));
        $profile->assertOk();
        $profile->assertSee('Cancel Appointment', false);
        $profile->assertSee('Reschedule Appointment', false);
        $profile->assertSee(route('appointments.reschedule-slots', $appointment), false);
    }

    public function test_repeated_bookings_create_payments_and_invoices_stay_stable(): void
    {
        [$doctor] = $this->doctorWithAvailability(scheduleEnd: '14:00', breakBetween: 0);
        $patient = $this->patient();
        $times = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30'];

        $this->actingAs($patient, 'patient');

        foreach ($times as $time) {
            $this->post(route('appointments.review'), $this->reviewPayload($doctor, $time))
                ->assertRedirect(route('appointments.payment'));

            $booking = $this->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital']);

            $appointment = Appointment::query()
                ->with('payment')
                ->where('patient_id', $patient->id)
                ->where('doctor_id', $doctor->id)
                ->whereDate('date', '2026-05-04')
                ->where('time', $time)
                ->firstOrFail();

            $booking->assertRedirect(route('appointments.invoice', $appointment));
            $this->assertNotNull($appointment->payment);

            $this->get(route('appointments.invoice', $appointment))
                ->assertOk()
                ->assertSee('Appointment Confirmed!', false)
                ->assertSee('Reference', false);

            $this->get(route('appointments.invoice', $appointment))->assertOk();
        }

        $this->assertSame(10, Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', '2026-05-04')
            ->whereIn('time', $times)
            ->count());
    }

    public function test_gateway_payment_methods_reach_invoice_after_verified_callback(): void
    {
        Http::fake([
            '*' => Http::response([
                'redirectUrl' => 'https://payments.example.test/checkout',
                'referenceNumber' => 'FAWRY-TEST-REF',
            ], 200),
        ]);

        config([
            'payment.fawry.merchant_code' => 'merchant',
            'payment.fawry.secure_key' => 'secret',
            'payment.fawry.verify_signature' => false,
        ]);

        [$doctor] = $this->doctorWithAvailability(scheduleEnd: '11:00', breakBetween: 0);
        $patient = $this->patient();
        $methods = [
            'fawry_wallet' => '09:00',
            'instapay' => '09:30',
        ];

        $this->actingAs($patient, 'patient');

        foreach ($methods as $method => $time) {
            $this->post(route('appointments.review'), $this->reviewPayload($doctor, $time))
                ->assertRedirect(route('appointments.payment'));

            $this->post(route('appointments.confirm'), ['payment_method' => $method])
                ->assertRedirect('https://payments.example.test/checkout');

            $appointment = Appointment::query()
                ->with('payment')
                ->where('patient_id', $patient->id)
                ->where('doctor_id', $doctor->id)
                ->where('time', $time)
                ->firstOrFail();

            $this->assertNotNull($appointment->payment);

            $callback = $this->get(route('payments.fawry.callback', [
                'merchantRefNum' => $appointment->payment->reference_number,
                'orderStatus' => 'PAID',
                'orderAmount' => $appointment->payment->amount,
                'referenceNumber' => 'FAWRY-' . $appointment->id,
            ]));

            $callback->assertRedirect(route('appointments.invoice', $appointment));
            $appointment->refresh();
            $this->assertSame('confirmed', $appointment->payment_status);
            $this->assertSame('confirmed', $appointment->payment()->first()?->status);

            $this->get(route('appointments.invoice', $appointment))
                ->assertOk()
                ->assertSee('Appointment Confirmed!', false);
        }
    }

    public function test_doctor_completes_confirmed_appointment_before_patient_can_rate(): void
    {
        [$doctor] = $this->doctorWithAvailability();
        $otherDoctor = Doctor::query()->create([
            'department_id' => $doctor->department_id,
            'name' => 'Other Doctor ' . uniqid(),
            'specialization' => 'Scheduling',
            'image' => 'doctor.jpg',
            'experience' => 5,
            'rating' => 4.5,
            'email' => 'other-doctor-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'status' => 'Available',
        ]);
        $patient = $this->patient();

        $this->actingAs($patient, 'patient')
            ->withSession(['booking_draft' => $this->bookingDraft($doctor, '09:00')])
            ->post(route('appointments.confirm'), ['payment_method' => 'pay_at_hospital'])
            ->assertRedirect();

        $appointment = Appointment::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('time', '09:00')
            ->firstOrFail();

        $this->post(route('doctor.ratings.store', $appointment), [
            'rating' => 5,
        ])->assertForbidden();

        $this->actingAs($otherDoctor, 'doctor')
            ->post(route('doctor.appointments.complete', $appointment))
            ->assertForbidden();

        $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.appointments.complete', $appointment))
            ->assertRedirect()
            ->assertSessionHas('success', 'Appointment marked as completed.');

        $appointment->refresh();
        $this->assertSame('Completed', $appointment->status);

        $this->actingAs($patient, 'patient')
            ->post(route('doctor.ratings.store', $appointment), [
                'rating' => 5,
                'comment' => 'Excellent doctor',
            ])
            ->assertRedirect(route('appointments.invoice', $appointment));

        $this->assertDatabaseHas('ratings', [
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'rating' => 5,
            'comment' => 'Excellent doctor',
        ]);
    }

    /**
     * @return array{0:Doctor,1:DoctorAvailability}
     */
    private function doctorWithAvailability(string $scheduleEnd = '10:00', int $breakBetween = 30): array
    {
        $department = Department::query()->create([
            'name_en' => 'Booking Slot Test ' . uniqid(),
            'name_ar' => 'Booking Slot Test',
            'image' => 'test.jpg',
            'status' => 'active',
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Booking Slot Doctor ' . uniqid(),
            'specialization' => 'Scheduling',
            'image' => 'doctor.jpg',
            'experience' => 5,
            'rating' => 4.5,
            'email' => 'booking-slot-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'status' => 'Available',
        ]);

        $availability = DoctorAvailability::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => 'hospital',
            'appointment_duration_minutes' => 30,
            'break_between_appointments_minutes' => $breakBetween,
            'booking_window_days' => 30,
            'min_notice_minutes' => 0,
            'timezone' => 'Africa/Cairo',
            'is_active' => true,
        ]);

        $availability->schedules()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => $scheduleEnd,
            'location_type' => 'hospital',
            'is_active' => true,
        ]);

        return [$doctor, $availability];
    }

    private function patient(): Patient
    {
        return Patient::query()->create([
            'national_id' => '29' . random_int(100000000000, 999999999999),
            'full_name' => 'Booking Test Patient',
            'dob' => '1990-01-01',
            'phone' => '01000000000',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function reviewPayload(Doctor $doctor, string $time): array
    {
        return [
            'doctor_id' => $doctor->id,
            'first_name' => 'Booking',
            'last_name' => 'Patient',
            'email' => 'booking-patient@example.com',
            'phone' => '01000000000',
            'reason' => 'Routine check',
            'time' => $time,
            'date' => '2026-05-04',
            'type' => 'hospital',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function bookingDraft(Doctor $doctor, string $time): array
    {
        return [
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'first_name' => 'Booking',
            'last_name' => 'Patient',
            'email' => 'booking-patient@example.com',
            'phone' => '01000000000',
            'reason' => 'Routine check',
            'time' => $time,
            'date' => '2026-05-04',
            'type' => 'hospital',
            'payment_amount' => 250.00,
            'clinic_name' => null,
            'clinic_address' => null,
            'clinic_phone' => null,
            'clinic_fee' => null,
            'clinic_notes' => null,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function readEnv(): array
    {
        $values = [];

        foreach (file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $values[$key] = trim($value, "\"'");
        }

        return $values;
    }
}
