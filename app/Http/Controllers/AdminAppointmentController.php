<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentAdminRequest;
use App\Models\AdminAppointment;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient','doctor'])->get();

        $patients = Patient::all();
        $doctors  = Doctor::all();

        return view('admin.appointments',
            compact('appointments','patients','doctors'));
    }
    public function store(StoreAppointmentAdminRequest $request)
    {
        $data = $request->validated();
        AdminAppointment::create($data);
        return redirect()->back()->with('success','Appointment Added Successfully');

    }
    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        return view('admin.appointments_edit', compact('appointment'));
    }
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.appointments')->with('success', 'Appointment updated');
    }
    public function delete($id)
    {
        Appointment::findOrFail($id)->delete();

        return redirect()->route('admin.appointments')->with('success', 'Appointment deleted');
    }
}
