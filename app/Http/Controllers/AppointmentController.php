<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient','doctor','department'])->latest()->get();
        return view('nurse.appointments', compact('appointments'));
    }
    public function create(Doctor $doctor, $time)
    {
        return view('appointments.create', compact('doctor', 'time'));
    }

    public function store(AppointmentRequest $request)
    {

        $data =$request->validated();

        $appointment = Appointment::create($data);

        return redirect()->route('appointments.success', $appointment->id);
    }
    public function success(Appointment $appointment)
    {
        return view('appointments.success', compact('appointment'));
    }
    public function start()
    {
        return redirect()->route('departments.index');
    }
}

