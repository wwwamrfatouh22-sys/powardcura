<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class AdminDoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::latest()->get();
        return view('admin.doctors', compact('doctors'));
    }
    public function create()
    {
        return view('admin.doctors_create');
    }

    public function store(Request $request)
    {
        Doctor::create([
            'name' => $request->name,
            'specialization' => $request->specialization,
            'experience' => $request->experience,
            'email' => $request->email,
            'status' => $request->status,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('admin.doctors')->with('success', 'Doctor added successfully');
    }

    public function edit($id)
    {
        $doctor = Doctor::findOrFail($id);
        return view('admin.doctors_edit', compact('doctor'));
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $doctor->update([
            'name' => $request->name,
            'specialization' => $request->specialization,
            'experience' => $request->experience,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.doctors')->with('success', 'Doctor updated successfully');
    }

    public function destroy($id)
    {
        Doctor::findOrFail($id)->delete();
        return redirect()->route('admin.doctors')->with('success', 'Doctor deleted');
    }
}
