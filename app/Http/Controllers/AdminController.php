<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Room;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPatients = Patient::count();
        $totalDoctors = Doctor::count();
        $totalAppointments = Appointment::count();
        $todayAppointments = Appointment::whereDate('date', today())->count();

        $totalRooms = Room::count();
        $availableRooms = Room::where('status','available')->count();

        return view('admin.dashboard', compact(
            'totalPatients',
            'totalDoctors',
            'totalAppointments',
            'todayAppointments',
            'totalRooms',
            'availableRooms'
        ));
    }
}
