<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\JobApplication;
use App\Models\Job;
use App\Models\Patient;
use App\Models\Rating;
use App\Models\Staff;
use App\Models\TrainingProgram;
use App\Models\TrainingRegistration;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\PrivateClinicBookingSupport;
use Tests\TestCase;

class LocalMysqlEndToEndTest extends TestCase
{
    use DatabaseTransactions;

    /** @var string[] */
    private array $storedFiles = [];

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
    }

    protected function tearDown(): void
    {
        foreach ($this->storedFiles as $path) {
            Storage::disk('public')->delete($path);
        }

        parent::tearDown();
    }

    public function test_homepage_departments_doctors_and_doctor_details_load(): void
    {
        $department = Department::query()->has('doctors')->firstOrFail();
        $doctor = $department->doctors()->firstOrFail();

        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee((string) $department->name_en, false);

        $departmentPage = $this->get(route('specialties.doctors', $department));
        $departmentPage->assertOk();
        $departmentPage->assertSee((string) $doctor->name, false);

        $doctorPage = $this->get(route('doctors.show', $doctor));
        $doctorPage->assertOk();
        $doctorPage->assertSee((string) $doctor->name, false);
    }

    public function test_booking_flow_blocks_missing_receipt_and_prevents_duplicate_slot(): void
    {
        $patient = Patient::query()->firstOrFail();
        $doctor = Doctor::query()->firstOrFail();
        [$date, $time] = $this->findAvailableHospitalSlot($doctor);

        $this->actingAs($patient, 'patient');

        $reviewPayload = [
            'doctor_id' => $doctor->id,
            'first_name' => 'Local',
            'last_name' => 'Patient',
            'email' => 'local.patient@example.com',
            'phone' => '01012345678',
            'reason' => 'Feature-test booking',
            'time' => $time,
            'date' => $date,
            'type' => 'hospital',
        ];

        $review = $this->post(route('appointments.review'), $reviewPayload);
        $review->assertRedirect(route('appointments.payment'));

        $withoutReceipt = $this->from(route('appointments.payment'))
            ->post(route('appointments.confirm'), [
                'payment_method' => 'instapay',
            ]);

        $withoutReceipt->assertRedirect(route('appointments.payment'));
        $withoutReceipt->assertSessionHasErrors(['receipt']);

        $receipt = UploadedFile::fake()->create('receipt.pdf', 32, 'application/pdf');
        $booking = $this->post(route('appointments.confirm'), [
            'payment_method' => 'instapay',
            'receipt' => $receipt,
        ]);

        $appointment = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', $date)
            ->where('time', $time)
            ->latest('id')
            ->first();

        $this->assertNotNull($appointment);
        $this->assertSame($patient->id, $appointment->patient_id);
        $this->assertSame($doctor->id, $appointment->doctor_id);
        $this->assertNotNull($appointment->payment_receipt_path);
        $this->storedFiles[] = $appointment->payment_receipt_path;

        $booking->assertRedirect(route('appointments.invoice', $appointment));

        $duplicateReview = $this->post(route('appointments.review'), $reviewPayload);
        $duplicateReview->assertRedirect();
        $duplicateReview->assertSessionHasErrors(['time']);
    }

    public function test_admin_dashboard_and_receipt_view_work(): void
    {
        $admin = Admin::query()->firstOrFail();
        $doctor = Doctor::query()->firstOrFail();
        $patient = Patient::query()->firstOrFail();
        $receiptPath = $this->storeFixtureFile('payments/receipts/probe-admin-receipt.pdf');

        $appointment = Appointment::query()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->addDays(5)->toDateString(),
            'time' => '16:00',
            'status' => 'Confirmed',
            'first_name' => 'Admin',
            'last_name' => 'Fixture',
            'email' => 'admin-fixture@example.com',
            'phone' => '01077777777',
            'reason' => 'Admin appointments page probe',
            'type' => 'hospital',
            'payment_status' => 'receipt_submitted',
            'payment_receipt_path' => $receiptPath,
        ]);

        $this->actingAs($admin, 'admin');

        $dashboard = $this->get(route('admin.dashboard'));
        $dashboard->assertOk();

        $appointmentsPage = $this->get(route('admin.appointments'));
        $appointmentsPage->assertOk();
        $appointmentsPage->assertSee('Admin Fixture', false);
        $appointmentsPage->assertSee((string) $doctor->name, false);

        $receipt = $this->get(route('admin.payments.receipt', $appointment));
        $receipt->assertOk();
    }

    public function test_doctor_appointments_only_show_today_and_tomorrow(): void
    {
        $doctor = Doctor::query()->firstOrFail();
        $patient = Patient::query()->firstOrFail();

        Appointment::query()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->toDateString(),
            'time' => '10:00',
            'status' => 'Confirmed',
            'first_name' => 'Today',
            'last_name' => 'Patient',
            'phone' => '01000000001',
            'type' => 'hospital',
        ]);

        Appointment::query()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->addDay()->toDateString(),
            'time' => '11:00',
            'status' => 'Confirmed',
            'first_name' => 'Tomorrow',
            'last_name' => 'Patient',
            'phone' => '01000000002',
            'type' => 'hospital',
        ]);

        Appointment::query()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '12:00',
            'status' => 'Confirmed',
            'first_name' => 'Future',
            'last_name' => 'Patient',
            'phone' => '01000000003',
            'type' => 'hospital',
        ]);

        $this->actingAs($doctor, 'doctor');

        $response = $this->get(route('doctor.appointments'));
        $response->assertOk();
        $response->assertSee('Today Patient', false);
        $response->assertSee('Tomorrow Patient', false);
        $response->assertDontSee('Future Patient', false);
    }

    public function test_complaint_submission_and_staff_complaint_open_work(): void
    {
        $patient = Patient::query()->firstOrFail();
        $staff = $this->createStaff();

        $this->actingAs($patient, 'patient');

        $submit = $this->post(route('complaints.store'), [
            'name' => 'Local Complaint',
            'email' => 'complaint@example.com',
            'phone' => '01012345678',
            'subject' => 'Probe Subject',
            'type' => 'Service',
            'department' => 'Cardiology',
            'details' => 'Probe complaint details',
        ]);

        $submit->assertRedirect('/');

        $complaint = Complaint::query()->where('email', 'complaint@example.com')->latest('id')->first();
        $this->assertNotNull($complaint);

        $this->actingAs($staff, 'staff');

        $index = $this->get(route('staff.complaints'));
        $index->assertOk();
        $index->assertSee('Probe Subject', false);
        $index->assertSee('View Complaint', false);
    }

    public function test_rating_submission_appears_in_admin_dashboard_and_not_on_doctor_appointments_page(): void
    {
        $patient = Patient::query()->firstOrFail();
        $admin = Admin::query()->firstOrFail();
        $doctor = Doctor::query()->firstOrFail();

        $appointment = Appointment::query()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->toDateString(),
            'time' => '14:00',
            'status' => 'Completed',
            'first_name' => 'Rated',
            'last_name' => 'Patient',
            'phone' => '01099999999',
            'type' => 'hospital',
        ]);

        $this->actingAs($patient, 'patient');

        $rate = $this->post(route('doctor.ratings.store', $appointment), [
            'rating' => 5,
            'comment' => 'Excellent service',
        ]);

        $rate->assertRedirect(route('appointments.invoice', $appointment));
        $this->assertDatabaseHas('ratings', [
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'rating' => 5,
            'comment' => 'Excellent service',
        ]);

        $this->actingAs($admin, 'admin');
        $dashboard = $this->get(route('admin.dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Excellent service', false);

        $this->actingAs($doctor, 'doctor');
        $doctorAppointments = $this->get(route('doctor.appointments'));
        $doctorAppointments->assertOk();
        $doctorAppointments->assertDontSee('Excellent service', false);
    }

    public function test_missing_files_return_not_found_instead_of_server_error(): void
    {
        $admin = Admin::query()->firstOrFail();
        $staff = $this->createStaff();
        $doctor = Doctor::query()->firstOrFail();
        $patient = Patient::query()->firstOrFail();
        $program = TrainingProgram::query()->first() ?: TrainingProgram::query()->create([
            'title' => 'Probe Program',
            'description' => 'Probe description',
            'duration_weeks' => 4,
            'department_id' => $doctor->department_id,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => now()->toDateString(),
            'time' => '15:00',
            'status' => 'Confirmed',
            'first_name' => 'Missing',
            'last_name' => 'Receipt',
            'phone' => '01022222222',
            'type' => 'hospital',
            'payment_receipt_path' => 'payments/receipts/does-not-exist.pdf',
        ]);

        $missingTrainingCv = TrainingRegistration::query()->create([
            'training_id' => $program->id,
            'department_id' => $doctor->department_id,
            'full_name' => 'Missing CV',
            'email' => 'missing-cv@example.com',
            'phone' => '01044444444',
            'national_id' => 'TRX1234567890',
            'cv_path' => 'training-registrations/cv/does-not-exist.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($admin, 'admin');
        $receipt = $this->get(route('admin.payments.receipt', $appointment));
        $receipt->assertNotFound();

        $this->actingAs($staff, 'staff');
        $cv = $this->get(route('staff.training.cv.view', $missingTrainingCv));
        $cv->assertNotFound();
    }

    public function test_management_pages_render_on_mysql_without_runtime_errors(): void
    {
        $admin = Admin::query()->firstOrFail();
        $staff = $this->createStaff();
        $doctor = Doctor::query()->firstOrFail();
        $department = Department::query()->firstOrFail();

        $jobCvPath = $this->storeFixtureFile('cvs/probe-job-cv.pdf');
        $trainingCvPath = $this->storeFixtureFile('training-registrations/cv/probe-training-cv.pdf');

        $job = Job::query()->create([
            'title' => 'Probe Job',
            'description' => 'Probe description',
            'requirements' => 'Probe requirements',
            'department' => $department->name_en ?? 'General',
            'location' => 'NUH',
            'salary' => '1000',
            'type' => 'medical',
            'status' => 'active',
        ]);

        JobApplication::query()->create([
            'job_id' => $job->id,
            'name' => 'Probe Applicant',
            'full_name' => 'Probe Applicant',
            'email' => 'probe-applicant@example.com',
            'phone' => '01055555555',
            'national_id' => 'JOB1234567890',
            'cv' => $jobCvPath,
            'cv_path' => $jobCvPath,
            'status' => 'pending',
        ]);

        $program = TrainingProgram::query()->first() ?: TrainingProgram::query()->create([
            'title' => 'Probe Program',
            'description' => 'Probe description',
            'duration_weeks' => 6,
            'department_id' => $department->id,
            'is_active' => true,
        ]);

        TrainingRegistration::query()->create([
            'training_id' => $program->id,
            'department_id' => $department->id,
            'full_name' => 'Probe Trainee',
            'email' => 'probe-trainee@example.com',
            'phone' => '01066666666',
            'national_id' => 'TRN1234567890',
            'cv_path' => $trainingCvPath,
            'status' => 'pending',
        ]);

        $this->actingAs($admin, 'admin');
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.appointments'))->assertOk();

        $this->actingAs($staff, 'staff');
        $this->get(route('staff.dashboard'))->assertOk();
        $this->get(route('staff.complaints'))->assertOk();
        $this->get(route('staff.jobs.index'))->assertOk();
        $this->get(route('staff.job.applications'))->assertOk();
        $this->get(route('staff.training.programs'))->assertOk();

        $this->actingAs($doctor, 'doctor');
        $this->get(route('doctor.profile'))->assertOk();
        $this->get(route('doctor.appointments'))->assertOk();
    }

    /**
     * @return array<string, string>
     */
    private function readEnv(): array
    {
        $values = [];

        foreach (file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $values[$key] = trim($value, "\"'");
        }

        return $values;
    }

    private function createStaff(): Staff
    {
        return Staff::query()->create([
            'name' => 'Probe Staff',
            'email' => 'probe-staff-'.uniqid().'@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    private function storeFixtureFile(string $path): string
    {
        Storage::disk('public')->put($path, 'probe');
        $this->storedFiles[] = $path;

        return $path;
    }

    /**
     * @return array{0:string,1:string}
     */
    private function findAvailableHospitalSlot(Doctor $doctor): array
    {
        for ($offset = 7; $offset <= 30; $offset++) {
            $date = Carbon::now()->addDays($offset)->toDateString();
            $bookedTimes = Appointment::query()
                ->where('doctor_id', $doctor->id)
                ->whereDate('date', $date)
                ->pluck('time')
                ->map(fn ($time) => substr((string) $time, 0, 5))
                ->all();

            foreach (PrivateClinicBookingSupport::HOSPITAL_SLOTS as $slot) {
                if (!in_array($slot, $bookedTimes, true)) {
                    return [$date, $slot];
                }
            }
        }

        throw new \RuntimeException('No free hospital slot was found for the booking probe.');
    }
}
