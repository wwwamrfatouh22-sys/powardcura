<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Models\Appointment;
use App\Models\DocumentSignature;
use App\Models\Doctor;
use App\Models\LabRequest;
use App\Models\LeaveRequest;
use App\Models\RadiologyRequest;
use App\Services\Scheduling\SlotGenerationService;
use App\Support\AppointmentSecurity;
use App\Support\PrivateClinicBookingSupport;
use App\Support\ProtectedFile;
use App\Support\TableFilters;
use App\Support\AuditLogger;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DoctorController extends Controller
{
    protected function annotateAppointmentDates($appointments)
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $annotated = $appointments->map(function (Appointment $appointment) use ($today, $tomorrow) {
            $appointment->isToday = $appointment->date === $today;
            $appointment->isTomorrow = $appointment->date === $tomorrow;

            return $appointment;
        });

        if ($appointments instanceof LengthAwarePaginator || $appointments instanceof Paginator) {
            $appointments->setCollection($annotated);

            return $appointments;
        }

        return $annotated;
    }

    protected function upcomingAppointmentsQuery(Doctor $doctor)
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        return $doctor->appointments()
            ->whereIn('date', [$today, $tomorrow]);
    }

    public function show(Request $request, Doctor $doctor)
    {
        $doctor->loadMissing(['department', 'privateClinic']);
        $selectedDate = $request->query('date', now()->toDateString());
        if (CarbonImmutable::parse($selectedDate)->lt(now()->startOfDay())) {
            $selectedDate = now()->toDateString();
        }
        $selectedType = PrivateClinicBookingSupport::normalizeType($request->query('type'));

        if ($selectedType === 'private' && !PrivateClinicBookingSupport::hasPrivateClinic($doctor)) {
            $selectedType = 'hospital';
        }

        $slotEndDate = CarbonImmutable::parse($selectedDate)
            ->addDays(6)
            ->toDateString();
        $blockingAppointments = AppointmentSecurity::blockingAppointments($doctor->id, $selectedDate, $slotEndDate)
            ->get(['id', 'date', 'time', 'type']);
        $bookedSlots = $blockingAppointments
            ->filter(fn (Appointment $appointment) => CarbonImmutable::parse((string) $appointment->date)->toDateString() === $selectedDate)
            ->pluck('time')
            ->map(fn ($time) => AppointmentSecurity::normalizeTime((string) $time))
            ->unique()
            ->values()
            ->all();

        $slotDays = $this->slotDaysForDoctor($doctor, $selectedType, $selectedDate, 7, $blockingAppointments);

        return view('doctors.show', compact('doctor', 'selectedDate', 'selectedType', 'bookedSlots', 'slotDays'));
    }

    public function bookedSlots(Request $request, Doctor $doctor): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'type' => ['nullable', 'in:hospital,private'],
        ]);

        $type = PrivateClinicBookingSupport::normalizeType($validated['type'] ?? 'hospital');
        $doctor->loadMissing('privateClinic');

        $blockingAppointments = AppointmentSecurity::blockingAppointments($doctor->id, $validated['date'])
            ->get(['id', 'date', 'time', 'type']);
        $bookedSlots = $blockingAppointments
            ->when(
                ($validated['type'] ?? null) !== null,
                fn (Collection $appointments) => $appointments->where('type', $type)
            )
            ->pluck('time')
            ->map(fn ($time) => AppointmentSecurity::normalizeTime((string) $time))
            ->unique()
            ->values()
            ->all();

        $slotDays = $this->slotDaysForDoctor($doctor, $type, $validated['date'], 1, $blockingAppointments);
        $selectedDay = $slotDays[0] ?? [
            'date' => $validated['date'],
            'available_count' => 0,
            'slots' => [],
            'doctor_available' => false,
        ];

        return response()->json([
            'doctor_id' => $doctor->id,
            'date' => $validated['date'],
            'type' => $type,
            'booked_times' => $bookedSlots,
            'is_date_available' => PrivateClinicBookingSupport::isDateAvailable($doctor, $type, $validated['date']),
            'doctor_available' => $selectedDay['doctor_available'],
            'message' => $selectedDay['doctor_available'] ? null : 'Doctor is not available',
            'available_count' => $selectedDay['available_count'],
            'slots' => $selectedDay['slots'],
        ]);
    }

    /**
     * @return array<int, array{date:string,label:string,available_count:int,doctor_available:bool,slots:array<int,array<string,mixed>>}>
     */
    private function slotDaysForDoctor(
        Doctor $doctor,
        string $type,
        string $startDate,
        int $days = 7,
        ?Collection $blockingAppointments = null
    ): array
    {
        $scheduleType = $type === 'private' ? 'private_clinic' : 'hospital';
        $timezone = config('app.timezone', 'Africa/Cairo');
        $start = CarbonImmutable::parse($startDate, $timezone)->startOfDay();
        $end = $start->addDays(max($days - 1, 0));

        $generatedSlots = collect(app(SlotGenerationService::class)->availability(
            $doctor->id,
            $scheduleType,
            $start,
            $end,
            null,
            $blockingAppointments
        ))
            ->groupBy('date');

        $daysPayload = [];

        for ($offset = 0; $offset < $days; $offset++) {
            $date = $start->addDays($offset);
            $dateString = $date->toDateString();
            $slots = $generatedSlots->get($dateString, collect())
                ->map(fn (array $slot) => [
                    'time' => AppointmentSecurity::normalizeTime((string) $slot['start_time']),
                    'end_time' => AppointmentSecurity::normalizeTime((string) $slot['end_time']),
                    'available' => (bool) $slot['available'],
                    'status' => (string) $slot['status'],
                    'label' => (string) $slot['label'],
                ])
                ->sortBy('time')
                ->values()
                ->all();

            $daysPayload[] = [
                'date' => $dateString,
                'label' => $date->translatedFormat('D, M j'),
                'available_count' => collect($slots)->where('available', true)->count(),
                'doctor_available' => collect($slots)->where('available', true)->isNotEmpty(),
                'slots' => $slots,
            ];
        }

        return $daysPayload;
    }

    public function profile()
    {
        $doctor = auth()->guard('doctor')->user();
        $doctorId = $doctor->id;

        $appointmentsCount = Appointment::where('doctor_id', $doctorId)->count();

        $todayAppointmentsCount = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', now())
            ->count();

        $patientsCount = Appointment::where('doctor_id', $doctorId)
            ->distinct('patient_id')
            ->count('patient_id');

        $signaturesCount = Schema::hasTable('document_signatures')
            ? DocumentSignature::where('doctor_id', $doctorId)
                ->whereNotNull('signature')
                ->distinct('document_id')
                ->count('document_id')
            : 0;

        $pendingAppointmentsCount = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'Pending')
            ->count();

        $completedAppointmentsCount = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'Completed')
            ->count();

        $signedDocumentsCount = $signaturesCount;

        $recentAppointments = $this->upcomingAppointmentsQuery($doctor)
            ->with('patient')
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        $ratings = new Collection();

        return view('doctors.profile', compact(
            'doctor',
            'appointmentsCount',
            'todayAppointmentsCount',
            'patientsCount',
            'signaturesCount',
            'pendingAppointmentsCount',
            'completedAppointmentsCount',
            'signedDocumentsCount',
            'recentAppointments'
            ,
            'ratings'
        ));
    }

    public function appointments(Request $request)
    {
        $doctor = auth()->guard('doctor')->user();

        $doctorAppointmentsQuery = $this->upcomingAppointmentsQuery($doctor)
            ->with(['patient', 'department', 'labRequests', 'radiologyRequests'])
            ->orderBy('date')
            ->orderBy('time');

        TableFilters::apply($doctorAppointmentsQuery, $request, [
            'date_column' => 'date',
            'type_column' => 'type',
            'status_column' => 'status',
        ]);

        $doctorAppointments = $this->annotateAppointmentDates($doctorAppointmentsQuery
            ->paginate(10, ['*'], 'doctor_page')
            ->appends($request->query()));

        $privateClinicAppointmentsQuery = $this->upcomingAppointmentsQuery($doctor)
            ->with(['patient', 'department', 'labRequests', 'radiologyRequests'])
            ->where('type', 'private')
            ->orderBy('date')
            ->orderBy('time');

        TableFilters::apply($privateClinicAppointmentsQuery, $request, [
            'date_column' => 'date',
            'type_column' => 'type',
            'status_column' => 'status',
        ]);

        $privateClinicAppointments = $this->annotateAppointmentDates($privateClinicAppointmentsQuery
            ->paginate(10, ['*'], 'private_page')
            ->appends($request->query()));

        return view('doctors.appointments', compact(
            'doctor',
            'doctorAppointments',
            'privateClinicAppointments'
        ));
    }

    public function appointmentsJson(): JsonResponse
    {
        $doctor = auth()->guard('doctor')->user();

        $appointments = $this->annotateAppointmentDates($this->upcomingAppointmentsQuery($doctor)
            ->with(['patient', 'doctor', 'department'])
            ->orderBy('date')
            ->orderBy('time')
            ->get())
            ->map(function (Appointment $appointment) {
                $patientName = trim((string) $appointment->first_name . ' ' . (string) $appointment->last_name);
                if ($patientName === '' && $appointment->patient) {
                    $patientName = (string) $appointment->patient->full_name;
                }

                return [
                    'id' => $appointment->id,
                    'doctor_id' => $appointment->doctor_id,
                    'patient_id' => $appointment->patient_id,
                    'patient_name' => $patientName !== '' ? $patientName : 'Unknown Patient',
                    'patient_code' => $appointment->patient?->file_number
                        ? 'P-' . $appointment->patient?->file_number
                        : 'APT-' . str_pad((string) $appointment->id, 4, '0', STR_PAD_LEFT),
                    'date' => $appointment->date,
                    'isToday' => (bool) ($appointment->isToday ?? false),
                    'isTomorrow' => (bool) ($appointment->isTomorrow ?? false),
                    'time' => $appointment->time,
                    'reason' => $appointment->reason ?: 'General consultation',
                    'type' => $appointment->type ?: 'hospital',
                    'status' => $appointment->status ?: 'Pending',
                    'department_name' => $appointment->department?->name_en,
                ];
            });

        return response()->json([
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->name,
            'data' => $appointments,
        ]);
    }

    public function completeAppointment(Appointment $appointment): RedirectResponse
    {
        $doctor = auth()->guard('doctor')->user();

        abort_unless($doctor instanceof Doctor && (int) $appointment->doctor_id === (int) $doctor->id, 403);

        if ($appointment->isCanceled()) {
            return back()->withErrors(['appointment' => 'Canceled appointments cannot be completed.']);
        }

        if (strtolower((string) $appointment->status) !== 'confirmed') {
            return back()->withErrors(['appointment' => 'Only confirmed appointments can be marked as completed.']);
        }

        $appointment->update(['status' => 'Completed']);

        AuditLogger::log('appointment.completed', $appointment->fresh(), [
            'source' => 'doctor_dashboard',
            'doctor_id' => $doctor->id,
        ]);

        return back()->with('success', 'Appointment marked as completed.');
    }

    public function storeDiagnosticRequest(Request $request, Appointment $appointment, string $type): RedirectResponse
    {
        $doctor = auth()->guard('doctor')->user();

        abort_unless($doctor instanceof Doctor && (int) $appointment->doctor_id === (int) $doctor->id, 403);
        abort_unless(in_array($type, ['lab', 'radiology'], true), 404);
        abort_unless($appointment->patient_id, 422, 'This appointment is not linked to a patient record.');

        $validated = $request->validate([
            'request_type' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'priority' => ['required', 'in:normal,urgent'],
        ]);

        $payload = [
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'phone' => $appointment->phone ?: $appointment->patient?->phone,
            'request_type' => trim($validated['request_type']),
            'notes' => $validated['notes'] ?? null,
            'priority' => $validated['priority'],
            'status' => 'pending',
        ];

        $record = $type === 'lab'
            ? LabRequest::query()->create($payload)
            : RadiologyRequest::query()->create($payload);

        AuditLogger::log('diagnostic_request.created', $record, [
            'type' => $type,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
        ]);

        return back()->with('success', ucfirst($type === 'lab' ? 'lab test' : 'radiology') . ' request sent to staff.');
    }

    public function downloadDiagnosticResult(string $type, int $id): StreamedResponse
    {
        $doctor = auth()->guard('doctor')->user();
        abort_unless($doctor instanceof Doctor, 403);
        abort_unless(in_array($type, ['lab', 'radiology'], true), 404);

        $requestRecord = ($type === 'lab' ? LabRequest::query() : RadiologyRequest::query())
            ->with('appointment')
            ->findOrFail($id);

        abort_unless((int) $requestRecord->doctor_id === (int) $doctor->id, 403);
        abort_unless($requestRecord->appointment && (int) $requestRecord->appointment->doctor_id === (int) $doctor->id, 403);
        abort_unless($requestRecord->uploaded_result, 404, 'No completed result is attached yet.');

        AuditLogger::log('diagnostic_request.doctor_downloaded', $requestRecord, [
            'type' => $type,
            'doctor_id' => $doctor->id,
        ]);

        return ProtectedFile::download($requestRecord->uploaded_result, 'diagnostic-result-' . $requestRecord->id);
    }

    public function storeLeaveRequest(StoreLeaveRequest $request)
    {
        $data = $request->validated();
        $data['doctor_id'] = auth()->guard('doctor')->id();

        LeaveRequest::create($data);

        return back()->with('success', 'Leave request submitted successfully.');
    }

    public function leaveForm()
    {
        $doctor = auth()->guard('doctor')->user();

        $leaveRequests = LeaveRequest::where('doctor_id', $doctor->id)
            ->latest()
            ->get();

        return view('doctors.leave-request', compact('doctor', 'leaveRequests'));
    }

}
