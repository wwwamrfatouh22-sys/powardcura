<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('patient')->get();
        return view('admin.rooms', compact('rooms'));
    }

    public function create()
    {
        return view('admin.create_rooms');
    }

    public function store(Request $request)
    {
        Room::create([
            'room_number' => $request->room_number,
            'type' => $request->type,
            'floor' => $request->floor,
            'capacity' => $request->capacity,
            'status' => $request->status,
            'current_patient' => $request->current_patient
        ]);

        return redirect()->route('admin.rooms')->with('success', 'Room Added Successfully');
    }
    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $patients = Patient::pluck('full_name', 'id');

        return view('admin.edit_room', compact('room', 'patients'));
    }
    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $room->update([
            'room_number' => $request->room_number,
            'type' => $request->type,
            'floor' => $request->floor,
            'capacity' => $request->capacity,
            'status' => $request->status,
            'patient_id' => $request->patient_id,
            'current_patient' => $request->current_patient,
        ]);

        return redirect()->route('admin.rooms')
            ->with('success', 'Room updated successfully');
    }
    public function destroy($id)
    {
        Room::findOrFail($id)->delete();

        return redirect()->route('admin.rooms')
            ->with('success', 'Room deleted successfully');
    }
}
