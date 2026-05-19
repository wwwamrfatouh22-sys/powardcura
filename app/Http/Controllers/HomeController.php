<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Room;
use App\Models\Department;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $patientsCount = Schema::hasTable('patients') ? Patient::count() : 0;
        $doctorsCount = Schema::hasTable('doctors') ? Doctor::count() : 0;
        $appointmentsCount = Schema::hasTable('appointments') ? Appointment::count() : 0;
        $todayAppointments = Schema::hasTable('appointments')
            ? Appointment::whereDate('date', today())->count()
            : 0;

        $roomsCount = Schema::hasTable('rooms') ? Room::count() : 0;
        $availableRooms = Schema::hasTable('rooms') ? Room::where('status', 'available')->count() : 0;
        $occupiedRooms = Schema::hasTable('rooms') ? Room::where('status', 'occupied')->count() : 0;

        $departments = Schema::hasTable('departments') ? Department::all() : collect();

        return view('home', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsCount',
            'todayAppointments',
            'roomsCount',
            'availableRooms',
            'occupiedRooms',
            'departments'
        ));
    }
}
