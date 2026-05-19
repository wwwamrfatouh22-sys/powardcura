<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Room;
use App\Models\WebsiteRating;
use App\Services\AdminHospitalAnalyticsService;
use App\Services\DoctorRatingDashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function dashboard(
        Request $request,
        DoctorRatingDashboardService $doctorRatingDashboardService,
        AdminHospitalAnalyticsService $hospitalAnalyticsService
    ) {
        $patientsCount = Schema::hasTable('patients') ? Patient::query()->withoutTrashed()->count() : 0;
        $doctorsCount = Schema::hasTable('doctors') ? Doctor::query()->withoutTrashed()->count() : 0;
        $appointmentsCount = Schema::hasTable('appointments') ? Appointment::query()->count() : 0;
        $roomsCount = Schema::hasTable('rooms') ? Room::count() : 0;

        $availableRooms = Schema::hasTable('rooms') ? Room::where('status', 'available')->count() : 0;
        $occupiedRooms = Schema::hasTable('rooms') ? Room::where('status', 'occupied')->count() : 0;

        $todayAppointments = Schema::hasTable('appointments')
            ? Appointment::whereDate('date', Carbon::today())->count()
            : 0;

        if ($doctorsCount === 0 || $appointmentsCount === 0) {
            Log::debug('Admin dashboard returned empty core data.', [
                'active_doctors' => $doctorsCount,
                'total_doctors_with_trashed' => Schema::hasTable('doctors') ? Doctor::withTrashed()->count() : 0,
                'appointments' => $appointmentsCount,
                'departments' => Schema::hasTable('departments') ? \App\Models\Department::query()->count() : 0,
            ]);
        }

        $ratingDashboard = $doctorRatingDashboardService->build();
        $hospitalAnalytics = $hospitalAnalyticsService->build();

        $websiteRatingsQuery = WebsiteRating::query()
            ->with(['appointment.doctor.department', 'appointment.department', 'patient'])
            ->latest();

        if ($request->filled('rating')) {
            $websiteRatingsQuery->where('rating', (int) $request->input('rating'));
        }

        if ($request->filled('payment_status')) {
            $websiteRatingsQuery->whereHas('appointment', function ($query) use ($request) {
                $query->where('payment_status', $request->string('payment_status'));
            });
        }

        $websiteRatings = $websiteRatingsQuery->paginate(10)->withQueryString();

        return view('admin.dashboard', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsCount',
            'roomsCount',
            'availableRooms',
            'occupiedRooms',
            'todayAppointments',
            'ratingDashboard',
            'hospitalAnalytics',
            'websiteRatings'
        ));
    }
}
