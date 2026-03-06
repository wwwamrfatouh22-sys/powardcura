<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\LeaveRequest;
use App\Models\Patient;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function show(Doctor $doctor)
    {
        return view('doctors.show', compact('doctor'));
    }
    public function profile()
    {
        $doctor = auth()->guard('doctor')->user();

        $appointmentsCount = Appointment::where('doctor_id', $doctor->id)->count();

        $patientsCount = Appointment::where('doctor_id', $doctor->id)
            ->distinct('patient_id')
            ->count('patient_id');

        $recentAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->take(5)
            ->get();

        return view('doctors.profile', compact(
            'doctor',
            'appointmentsCount',
            'patientsCount',
            'recentAppointments'
        ));
    }
    public function appointments()
    {
        $doctor = auth()->guard('doctor')->user();

        $hospitalAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('type', 'hospital')
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $privateAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('type', 'private')
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('doctors.appointments', compact(
            'hospitalAppointments',
            'privateAppointments'
        ));
    }

    public function storeLeaveRequest(StoreLeaveRequest  $request)
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
//    public function storeAppointment(Request $request)
//    {
//        $data = auth()->guard('doctor')->user();
//
//        Appointment::create($data);
//
//        return back()->with('success', 'Appointment created successfully');
//    }
}
